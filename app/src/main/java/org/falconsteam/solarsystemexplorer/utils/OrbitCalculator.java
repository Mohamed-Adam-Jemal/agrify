package org.falconsteam.solarsystemexplorer.utils;

public class OrbitCalculator {

    // The center of the solar system (Sun position on canvas)
    private final double centerX;
    private final double centerY;

    // Scale factor: converts AU distance to pixels
    private final double auToPixels;

    public OrbitCalculator(double centerX, double centerY, double auToPixels) {
        this.centerX   = centerX;
        this.centerY   = centerY;
        this.auToPixels = auToPixels;
    }

    /**
     * Calculates the X position of a planet on its orbit
     * @param distanceFromSun  in AU
     * @param angleDegrees     current angle in degrees (0-360)
     */
    public double calculateX(double distanceFromSun, double angleDegrees) {
        double angleRad = Math.toRadians(angleDegrees);
        double orbitRadius = distanceFromSun * auToPixels;
        return centerX + orbitRadius * Math.cos(angleRad);
    }

    /**
     * Calculates the Y position of a planet on its orbit
     * @param distanceFromSun  in AU
     * @param angleDegrees     current angle in degrees (0-360)
     */
    public double calculateY(double distanceFromSun, double angleDegrees) {
        double angleRad = Math.toRadians(angleDegrees);
        double orbitRadius = distanceFromSun * auToPixels;
        return centerY + orbitRadius * Math.sin(angleRad);
    }

    /**
     * Advances the angle based on the planet's speed factor
     * @param currentAngle     current angle in degrees
     * @param speedFactor      from planets.json (orbitalSpeedFactor)
     * @param deltaMultiplier  animation speed multiplier (e.g. 1.0 = normal)
     * @return new angle (wraps around at 360)
     */
    public double nextAngle(double currentAngle, double speedFactor, double deltaMultiplier) {
        double newAngle = currentAngle + (speedFactor * deltaMultiplier);
        return newAngle % 360;
    }

    /**
     * Returns the orbit radius in pixels for drawing the orbit path
     */
    public double getOrbitRadius(double distanceFromSun) {
        return distanceFromSun * auToPixels;
    }
}