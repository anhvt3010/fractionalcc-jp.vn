$ErrorActionPreference = 'Stop'
$projectRoot = Split-Path -Parent $PSScriptRoot
$envPath = Join-Path $projectRoot '.env'
if (Test-Path -LiteralPath $envPath) {
    Write-Output '.env already exists. Existing local credentials were preserved.'
    exit 0
}

function New-LocalSecret {
    $randomBytes = New-Object byte[] 24
    $generator = [Security.Cryptography.RandomNumberGenerator]::Create()
    try { $generator.GetBytes($randomBytes) } finally { $generator.Dispose() }
    return [BitConverter]::ToString($randomBytes).Replace('-', '').ToLowerInvariant()
}

$envText = [IO.File]::ReadAllText((Join-Path $projectRoot '.env.example'))
foreach ($secretName in @('DB_PASSWORD', 'DB_ROOT_PASSWORD', 'WP_ADMIN_PASSWORD')) {
    $envText = [regex]::Replace($envText, ('(?m)^' + $secretName + '=[^\r\n]*'), ($secretName + '=' + (New-LocalSecret)))
}
[IO.File]::WriteAllText($envPath, $envText, (New-Object Text.UTF8Encoding($false)))
Write-Output 'Created private .env with independent random local passwords. Do not commit this file.'
