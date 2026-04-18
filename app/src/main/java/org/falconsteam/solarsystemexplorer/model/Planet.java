package org.falconsteam.solarsystemexplorer.model;

public class Planet extends CelestialBody {
    public Planet(String name, double distanceFromSun, double radiusKm, String description) {
        super(name, distanceFromSun, radiusKm, description);
    }
    @Override public String getType() { return "Planet"; }
}