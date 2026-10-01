from pathlib import Path

from PIL import Image, ImageDraw


ROOT = Path(__file__).resolve().parents[1]
BRAND = ROOT / "public" / "images" / "brand"


def render(size: int) -> Image.Image:
    scale = size / 64
    image = Image.new("RGBA", (size, size), "#07111f")
    draw = ImageDraw.Draw(image)

    radius = round(14 * scale)
    draw.rounded_rectangle((0, 0, size - 1, size - 1), radius=radius, fill="#07111f")

    def points(values):
        return [(round(x * scale), round(y * scale)) for x, y in values]

    draw.polygon(points([(30.56, 10.44), (30.13, 20.35), (21.09, 29.39), (10.44, 30.56), (30.56, 30.56)]), fill="#f8fafc")
    draw.polygon(points([(33.44, 10.44), (33.87, 20.35), (42.91, 29.39), (53.56, 30.56), (33.44, 30.56)]), fill="#22d3ee")
    draw.polygon(points([(10.44, 33.44), (21.09, 34.61), (30.13, 43.65), (30.56, 53.56), (30.56, 33.44)]), fill="#60a5fa")
    draw.polygon(points([(33.44, 33.44), (33.44, 53.56), (33.87, 43.65), (42.91, 34.61), (53.56, 33.44)]), fill="#2563eb")
    return image


BRAND.mkdir(parents=True, exist_ok=True)
render(512).save(BRAND / "icon-512.png", optimize=True)
render(192).save(BRAND / "icon-192.png", optimize=True)
render(180).save(BRAND / "apple-touch-icon.png", optimize=True)
render(64).save(
    ROOT / "public" / "favicon.ico",
    format="ICO",
    sizes=[(16, 16), (32, 32), (48, 48), (64, 64)],
)
