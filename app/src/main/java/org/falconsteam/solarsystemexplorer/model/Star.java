package org.falconsteam.solarsystemexplorer.model;

public class Star extends CelestialBody {
    public Star(String name, double distanceFromSun, double radiusKm, String description) {
        super(name, distanceFromSun, radiusKm, description);
    }
    @Override public String getType() { return "Star"; }
}