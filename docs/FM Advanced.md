# Common Pitfalls

### ADEP ‘OR’ Logic
The AND/OR logic of our system is pretty fixed to allow simple interpretation

# Prohibit measure - Compound Restrictions


# Example Specific Measures

### Ground Stop

A ground stop (i.e. stopping departures to an airport) shall preferably be issued as a ‘rate per hour’ flow measure with a value of 0. While a prohibit flow measure could be issued for the same reason, the interpretation of prohibit measures is more complex and may lead controllers to searching for other flow measures to find a suitable route. Rate per hour of 0 is not only ambiguous, but allows the measure to be edited and progressively lifted.

### Re-route from point to point

Where you wish for all traffic to be re-routed from one point/route to another point/route, use the ‘mandatory route’ measure in isolation. Issue the mandatory route via a new waypoint/route, with a filter of the old (incorrect) waypoint/route. It is helpful to include route strings in the ‘reason’ field to assist controllers making the re-route.

When deciding who should be issuing this re-route, consider distance from your airport and use the FAO to include only close FIRs. Ground stations receiving the re-route will be expected to enforce the re-route regardless. Airborne traffic are expected to be re-routed by the FIR where the start of the re-route occurs.

For the example of re-routing traffic to Dublin (EIDW) away from “ABLIN” and instead to “VATRY”.

> Type: Mandatory Route
> Value: 
> Mandatory Route: VATRY
> ADEP: \*\*\*\*
> ADES: EIDW
> Waypoint: ABLIN
> FAO: London, Brest, Paris, Brussels

### Re-route without preference

Where a particular route needs to be avoided, however there are either multiple re-route options, or the re-route choice does not matter, use a ‘Prohibit’ measure. 

For the example of an event with slot bookings between airport XXXX and YYYY whereby all traffic will be routing via ‘ABCDE’, it may be preferable for traffic without a slot to take an alternate route.

> Type: Prohibit
> Value: 
> ADEP: XXXX
> ADES: YYYY
> Waypoint: ABCDE
> Member Not Event: ‘My Event Name’
> FAO: FIR Name


# FAO tagging




# Editing Flow Measures
