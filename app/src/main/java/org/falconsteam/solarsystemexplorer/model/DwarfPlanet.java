package org.falconsteam.solarsystemexplorer.model;

public class DwarfPlanet extends CelestialBody {
    public DwarfPlanet(String name, double distanceFromSun, double radiusKm, String description,
                       int orbitalPeriodDays, double orbitalSpeedFactor, String color, int displayRadius) {
        super(name, distanceFromSun, radiusKm, description, orbitalPeriodDays, orbitalSpeedFactor, color, displayRadius);
    }

    @Override public String getType() { return "Dwarf Planet"; }
}