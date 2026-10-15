Add-Type -AssemblyName System.Drawing
$bmp = [System.Drawing.Bitmap]::FromFile("C:\Users\Admin\Desktop\images\login page.jpeg")

# Crop illustration exactly
$rect = New-Object System.Drawing.Rectangle(0, 285, 490, 429)
$cropped = $bmp.Clone($rect, $bmp.PixelFormat)
$cropped.Save("c:\xampp\htdocs\HomeServiceHub\public\images\login-illustration.png", [System.Drawing.Imaging.ImageFormat]::Png)
$cropped.Dispose()

# Also let's extract the brand logo icon (house icon + text) from top-left (x=50, y=60, width=220, height=50)
$logoRect = New-Object System.Drawing.Rectangle(55, 60, 220, 50)
$logoCropped = $bmp.Clone($logoRect, $bmp.PixelFormat)
$logoCropped.Save("c:\xampp\htdocs\HomeServiceHub\public\images\login-brand.png", [System.Drawing.Imaging.ImageFormat]::Png)
$logoCropped.Dispose()

# Also let's extract Google G icon from the button in the card if needed
# G icon is around x=775, y=520, width=24, height=24
for ($y = 500; $y -lt 550; $y++) {
    for ($x = 760; $x -lt 820; $x++) {
        $c = $bmp.GetPixel($x, $y)
        # Google red is ~ 234, 67, 53
        if ($c.R -gt 220 -and $c.G -lt 80 -and $c.B -lt 70) {
            Write-Host "Google red pixel at x=$x, y=$y"
            break
        }
    }
}

$bmp.Dispose()
Write-Host "Extracted assets successfully"
