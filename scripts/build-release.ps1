param(
    [Parameter(Mandatory=$true)]
    [ValidatePattern('^\d+\.\d+\.\d+(?:-[a-zA-Z0-9.-]+)?$')]
    [string]$Version
)
$ErrorActionPreference = 'Stop'
$taskPhp = @('C:/xampp/php/php.exe', 'D:/xampp/php/php.exe') | Where-Object { Test-Path -LiteralPath $_ } | Select-Object -First 1
if (-not $taskPhp) { throw 'XAMPP PHP executable not found.' }
$taskRoot = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
# Generate only public theme artifacts; fonts and licenses are already vendored.
$taskPhpOptions = @()
$taskHasWebp = & $taskPhp -r "echo function_exists('imagewebp') ? '1' : '0';"
if ($taskHasWebp -ne '1') { $taskPhpOptions = @('-d', 'extension=gd') }
& $taskPhp @taskPhpOptions (Join-Path $PSScriptRoot 'generate-fractional-webp.php')
if ($LASTEXITCODE -ne 0) { throw 'Theme image build failed.' }
& $taskPhp (Join-Path $PSScriptRoot 'minify-assets.php')
if ($LASTEXITCODE -ne 0) { throw 'Theme asset bundle failed.' }
$taskReleaseRoot = Join-Path $taskRoot 'release'
$taskVersionRoot = [System.IO.Path]::GetFullPath((Join-Path $taskReleaseRoot ('v' + $Version)))
if (-not $taskVersionRoot.StartsWith($taskReleaseRoot + [System.IO.Path]::DirectorySeparatorChar,[System.StringComparison]::OrdinalIgnoreCase)) { throw 'Release target is outside release/.' }
New-Item -ItemType Directory -Path $taskVersionRoot -Force | Out-Null
Add-Type -AssemblyName System.IO.Compression.FileSystem
Add-Type -AssemblyName System.IO.Compression
$taskPackages = @(
    @{Slug='project-core';Source=(Join-Path $taskRoot 'wp-content/plugins/project-core');Header='project-core.php'},
    @{Slug='project-child';Source=(Join-Path $taskRoot 'wp-content/themes/project-child');Header='style.css'}
)
$taskChecksums = @()
foreach($taskPackage in $taskPackages) {
    $taskHeader = [System.IO.File]::ReadAllText((Join-Path $taskPackage.Source $taskPackage.Header))
    if ($taskHeader -notmatch ('(?m)Version:\s*' + [regex]::Escape($Version) + '\s*$')) { throw "Source version differs for $($taskPackage.Slug)." }
    $taskArchive = Join-Path $taskVersionRoot ($taskPackage.Slug + '-v' + $Version + '.zip')
    if(Test-Path -LiteralPath $taskArchive) { throw "Archive already exists: $taskArchive. Use a new version or review the existing artifact." }
    $taskZip = [System.IO.Compression.ZipFile]::Open($taskArchive,[System.IO.Compression.ZipArchiveMode]::Create)
    try {
        foreach($taskFile in Get-ChildItem -LiteralPath $taskPackage.Source -File -Recurse) {
            $taskRelative = $taskFile.FullName.Substring($taskPackage.Source.Length + 1).Replace('\','/')
            if($taskRelative -match '(^|/)(\.env[^/]*|wp-config\.php|node_modules|\.git|cache|backups)(/|$)|\.(log|sql|sql\.gz)$') { throw "Forbidden runtime/private file in package: $taskRelative" }
            if($taskFile.Extension -eq '.php') {
                $taskLint = & $taskPhp -l $taskFile.FullName 2>&1
                if($LASTEXITCODE -ne 0) { throw ($taskLint -join [Environment]::NewLine) }
            }
            [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($taskZip,$taskFile.FullName,($taskPackage.Slug + '/' + $taskRelative),[System.IO.Compression.CompressionLevel]::Optimal) | Out-Null
        }
    } finally { $taskZip.Dispose() }
    $taskChecksums += (Get-FileHash -LiteralPath $taskArchive -Algorithm SHA256)
}
$taskChecksums | Select-Object Path,Hash | Format-Table -AutoSize
