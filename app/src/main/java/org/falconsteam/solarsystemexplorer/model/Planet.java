package org.falconsteam.solarsystemexplorer.model;

public class Planet extends CelestialBody {
    public Planet(String name, double distanceFromSun, double radiusKm, String description,
                  int orbitalPeriodDays, double orbitalSpeedFactor, String color, int displayRadius) {
        super(name, distanceFromSun, radiusKm, description, orbitalPeriodDays, orbitalSpeedFactor, color, displayRadius);
    }

    @Override public String getType() { return "Planet"; }
}