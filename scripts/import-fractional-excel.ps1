param(
    [string]$WorkbookPath = ''
)
$ErrorActionPreference = 'Stop'
$taskRoot = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
if (-not $WorkbookPath) {
    $taskCandidates = @(Get-ChildItem -LiteralPath (Join-Path $taskRoot 'template/fractional-cc') -Filter 'List *fractional CC.xlsx' -File)
    if ($taskCandidates.Count -ne 1) { throw 'Provide the exact approved workbook path.' }
    $WorkbookPath = $taskCandidates[0].FullName
}
Add-Type -AssemblyName System.IO.Compression.FileSystem
$taskArchive = [System.IO.Compression.ZipFile]::OpenRead((Resolve-Path -LiteralPath $WorkbookPath))
function Read-FccWorkbookXml([string]$Name) {
    $taskEntry = $taskArchive.GetEntry($Name)
    if (-not $taskEntry) { return $null }
    $taskReader = [System.IO.StreamReader]::new($taskEntry.Open())
    try { return [xml]$taskReader.ReadToEnd() } finally { $taskReader.Dispose() }
}
try {
    $taskBook = Read-FccWorkbookXml 'xl/workbook.xml'
    $taskBookRels = Read-FccWorkbookXml 'xl/_rels/workbook.xml.rels'
    $taskSheets = @($taskBook.SelectNodes('//*[local-name()="sheet"]'))
    if ($taskSheets.Count -ne 1) { throw 'Expected the approved single-sheet product workbook.' }
    $taskRelationId = $taskSheets[0].GetAttribute('id', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships')
    $taskTarget = ($taskBookRels.DocumentElement.ChildNodes | Where-Object { $_.Id -eq $taskRelationId }).Target
    $taskSheetPath = if ($taskTarget.StartsWith('/')) { $taskTarget.TrimStart('/') } else { 'xl/' + $taskTarget }
    $taskSheet = Read-FccWorkbookXml $taskSheetPath
    $taskStringsXml = Read-FccWorkbookXml 'xl/sharedStrings.xml'
    $taskStrings = @($taskStringsXml.SelectNodes('//*[local-name()="si"]') | ForEach-Object { ($_.SelectNodes('.//*[local-name()="t"]') | ForEach-Object { $_.InnerText }) -join '' })
    $taskCells = @{}
    foreach ($taskCell in $taskSheet.SelectNodes('//*[local-name()="sheetData"]/*[local-name()="row"]/*[local-name()="c"]')) {
        if ($taskCell.SelectSingleNode('./*[local-name()="f"]')) { throw 'Formula cells require separate validation before import.' }
        $taskValueNode = $taskCell.SelectSingleNode('./*[local-name()="v"]')
        $taskValue = if ($taskValueNode) { $taskValueNode.InnerText } else { '' }
        if ($taskCell.t -eq 's') { $taskValue = $taskStrings[[int]$taskValue] }
        elseif ($taskCell.t -eq 'inlineStr') { $taskValue = ($taskCell.SelectNodes('.//*[local-name()="t"]') | ForEach-Object { $_.InnerText }) -join '' }
        if ($taskValue -ne '') { $taskCells[$taskCell.r] = $taskValue }
    }
    $taskDrawing = Read-FccWorkbookXml 'xl/drawings/drawing1.xml'
    $taskDrawingRels = Read-FccWorkbookXml 'xl/drawings/_rels/drawing1.xml.rels'
    $taskImages = @()
    foreach ($taskAnchor in $taskDrawing.DocumentElement.ChildNodes) {
        $taskFrom = $taskAnchor.SelectSingleNode('./*[local-name()="from"]')
        $taskBlip = $taskAnchor.SelectSingleNode('.//*[local-name()="blip"]')
        if (-not $taskBlip -or -not $taskFrom) { continue }
        $taskImageId = $taskBlip.GetAttribute('embed', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships')
        $taskImageTarget = ($taskDrawingRels.DocumentElement.ChildNodes | Where-Object { $_.Id -eq $taskImageId }).Target
        $taskImagePath = if ($taskImageTarget.StartsWith('../')) { 'xl/' + $taskImageTarget.Substring(3) } elseif ($taskImageTarget.StartsWith('/')) { $taskImageTarget.TrimStart('/') } else { 'xl/drawings/' + $taskImageTarget }
        if ($taskImagePath -notmatch '^xl/media/image\d+\.png$') { throw 'Unexpected embedded image format/path.' }
        $taskImageEntry = $taskArchive.GetEntry($taskImagePath)
        $taskMemory = [System.IO.MemoryStream]::new()
        $taskStream = $taskImageEntry.Open()
        try { $taskStream.CopyTo($taskMemory) } finally { $taskStream.Dispose() }
        $taskImages += @{
            row = [int]$taskFrom.SelectSingleNode('./*[local-name()="row"]').InnerText + 1
            column = [int]$taskFrom.SelectSingleNode('./*[local-name()="col"]').InnerText + 1
            data = [Convert]::ToBase64String($taskMemory.ToArray())
        }
        $taskMemory.Dispose()
    }
    $taskPayload = @{
        file = [System.IO.Path]::GetFileName($WorkbookPath)
        sha256 = (Get-FileHash -LiteralPath $WorkbookPath -Algorithm SHA256).Hash.ToLowerInvariant()
        sheet = $taskSheets[0].name
        cells = $taskCells
        images = $taskImages
    }
    $taskTemp = Join-Path $env:TEMP ('fcc-excel-source-' + [guid]::NewGuid().ToString('N') + '.json')
    [System.IO.File]::WriteAllText($taskTemp, ($taskPayload | ConvertTo-Json -Depth 8), [System.Text.UTF8Encoding]::new($false))
    try {
        & node (Join-Path $PSScriptRoot 'import-fractional-excel.mjs') $taskTemp $taskRoot
        if ($LASTEXITCODE -ne 0) { throw 'Product defaults import failed.' }
    } finally { Remove-Item -LiteralPath $taskTemp }
} finally { $taskArchive.Dispose() }
