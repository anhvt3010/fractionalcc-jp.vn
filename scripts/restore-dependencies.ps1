$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot

function Restore-WordPressDependency {
    param([string]$RelativePath, [string]$Bootstrap, [string]$Version, [string]$ArchiveUrl)
    $destination = Join-Path $projectRoot $RelativePath
    if (Test-Path -LiteralPath $destination) {
        $bootstrapPath = Join-Path $destination $Bootstrap
        if (-not (Test-Path -LiteralPath $bootstrapPath -PathType Leaf)) {
            throw "Existing dependency is incomplete: $RelativePath. Inspect it before restoring."
        }
        $header = [IO.File]::ReadAllText($bootstrapPath)
        if ($header -notmatch ('(?m)^[\s*]*Version:\s*' + [regex]::Escape($Version) + '\s*$')) {
            throw "Existing dependency version differs from $Version at $RelativePath. Review compatibility before changing it."
        }
        Write-Output "Dependency already available: $RelativePath ($Version)"
        return
    }
    $staging = Join-Path $env:TEMP ('wordpress-dependency-' + [guid]::NewGuid().ToString('N'))
    New-Item -ItemType Directory -Path $staging | Out-Null
    $archive = Join-Path $staging 'dependency.zip'
    Invoke-WebRequest -Uri $ArchiveUrl -OutFile $archive -UseBasicParsing
    Expand-Archive -LiteralPath $archive -DestinationPath $staging
    $folderName = Split-Path -Leaf $destination
    $extracted = Join-Path $staging $folderName
    if (-not (Test-Path -LiteralPath (Join-Path $extracted $Bootstrap) -PathType Leaf)) {
        throw 'Unexpected dependency archive layout.'
    }
    New-Item -ItemType Directory -Path (Split-Path -Parent $destination) -Force | Out-Null
    if (Test-Path -LiteralPath $destination) { throw 'Destination appeared during download; refusing to overwrite.' }
    Copy-Item -LiteralPath $extracted -Destination $destination -Recurse
    Write-Output "Restored $RelativePath ($Version) from WordPress.org."
}

Restore-WordPressDependency -RelativePath 'wp-content/themes/kadence' -Bootstrap 'style.css' -Version '1.5.2' -ArchiveUrl 'https://downloads.wordpress.org/theme/kadence.1.5.2.zip'
Restore-WordPressDependency -RelativePath 'wp-content/plugins/advanced-custom-fields' -Bootstrap 'acf.php' -Version '6.8.10' -ArchiveUrl 'https://downloads.wordpress.org/plugin/advanced-custom-fields.6.8.10.zip'
