package org.falconsteam.solarsystemexplorer.view;

import org.falconsteam.solarsystemexplorer.model.CelestialBody;

import javafx.scene.image.Image;

public class PlanetAssets {

    public static Image loadImage(String name, double width, double height) {
        String file = switch (name) {
            case "Mercury" -> "mercury";
            case "Venus"   -> "venus";
            case "Earth"   -> "earth";
            case "Mars"    -> "mars";
            case "Jupiter" -> "jupiter";
            case "Saturn"  -> "saturn";
            case "Uranus"  -> "uranus";
            case "Neptune" -> "neptune";
            case "Pluto"   -> "pluto";
            case "The Sun" -> "sun";
            default        -> null;
        };

        if (file == null) return null;

        try {
            var stream = PlanetAssets.class
                .getResourceAsStream("/images/" + file + ".jpg");
            if (stream == null) {
                System.err.println("Image not found: " + file + ".jpg");
                return null;
            }
            return new Image(stream, width, height, true, true);
        } catch (Exception e) {
            System.err.println("Failed to load image: " + file + " — " + e.getMessage());
            return null;
        }
    }

    public static String getBadgeClass(CelestialBody body) {
        return switch (body.getType()) {
            case "Star"         -> "badge-star";
            case "Dwarf Planet" -> "badge-dwarf";
            default             -> "badge-planet";
        };
    }
}