param(
    [Parameter(Mandatory=$true)][ValidatePattern('^fcc-release-qa-\d+(?:-upgrade)?$')][string]$Name,
    [Parameter(Mandatory=$true)][ValidatePattern('^\d+\.\d+\.\d+$')][string]$Version
)
$ErrorActionPreference = 'Stop'
$taskSource = [IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
$taskTarget = [IO.Path]::GetFullPath(('C:/xampp/htdocs/' + $Name))
if (Test-Path -LiteralPath $taskTarget) { throw 'A new fixture directory is required.' }
foreach ($taskSlug in @('core','child')) {
    if (-not (Test-Path -LiteralPath ($taskSource + '/release/v' + $Version + '/project-' + $taskSlug + '-v' + $Version + '.zip'))) { throw 'Release ZIP missing.' }
}
New-Item -ItemType Directory -Path $taskTarget | Out-Null
Get-ChildItem -LiteralPath $taskSource -File -Filter '*.php' | Where-Object {$_.Name -ne 'wp-config.php'} | Copy-Item -Destination $taskTarget
foreach ($taskDir in @('wp-admin','wp-includes')) { Copy-Item -LiteralPath ($taskSource + '/' + $taskDir) -Destination $taskTarget -Recurse }
New-Item -ItemType Directory -Path ($taskTarget+'/wp-content/themes'),($taskTarget+'/wp-content/plugins'),($taskTarget+'/wp-content/mu-plugins') | Out-Null
Copy-Item -LiteralPath ($taskSource+'/wp-content/themes/kadence') -Destination ($taskTarget+'/wp-content/themes') -Recurse
Copy-Item -LiteralPath ($taskSource+'/wp-content/plugins/advanced-custom-fields') -Destination ($taskTarget+'/wp-content/plugins') -Recurse
$taskUtf8 = New-Object System.Text.UTF8Encoding($false)
[IO.File]::WriteAllText($taskTarget+'/wp-content/mu-plugins/block-test-mail.php', "<?php`nadd_filter('pre_wp_mail', '__return_true');`n", $taskUtf8)
$taskHtaccess = @"
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /$Name/
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /$Name/index.php [L]
</IfModule>
"@
[IO.File]::WriteAllText($taskTarget+'/.htaccess',$taskHtaccess,$taskUtf8)
Add-Type -AssemblyName System.IO.Compression.FileSystem
[IO.Compression.ZipFile]::ExtractToDirectory($taskSource+'/release/v'+$Version+'/project-core-v'+$Version+'.zip',$taskTarget+'/wp-content/plugins')
[IO.Compression.ZipFile]::ExtractToDirectory($taskSource+'/release/v'+$Version+'/project-child-v'+$Version+'.zip',$taskTarget+'/wp-content/themes')
$env:FCC_QA_NAME = $Name
& 'C:/xampp/php/php.exe' ($PSScriptRoot+'/setup-release-qa.php') configure
if ($LASTEXITCODE -ne 0) { throw 'Fixture configuration failed.' }
$env:PROJECT_WORDPRESS_ROOT = $taskTarget
& 'C:/xampp/php/php.exe' ($PSScriptRoot+'/setup-release-qa.php') install
if ($LASTEXITCODE -ne 0) { throw 'Fixture installation failed.' }
