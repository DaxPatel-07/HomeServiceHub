Add-Type -AssemblyName System.Drawing
$imgPath = "C:\Users\Admin\Desktop\images\login page.jpeg"
$bmp = [System.Drawing.Bitmap]::FromFile($imgPath)

# Let's see dimensions
Write-Host "Width: $($bmp.Width), Height: $($bmp.Height)"

# Crop illustration on the left
# House top starts around y=310, workers feet reach near bottom y=710, left x=10 to x=510
$rect = New-Object System.Drawing.Rectangle(15, 305, 500, 395)
$cropped = $bmp.Clone($rect, $bmp.PixelFormat)
$cropped.Save("c:\xampp\htdocs\HomeServiceHub\public\images\workers-illustration.png", [System.Drawing.Imaging.ImageFormat]::Png)
$cropped.Dispose()

# Also copy the full image so we have it
Copy-Item "C:\Users\Admin\Desktop\images\login page.jpeg" "c:\xampp\htdocs\HomeServiceHub\public\images\login-page-reference.jpeg" -Force

$bmp.Dispose()
Write-Host "Cropped successfully"
