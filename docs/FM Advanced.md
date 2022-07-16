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

Selecting the right FIRs to ‘tag’ determines where the flow measures will be sent to (see FM: Types). This provides the useful function that ‘far away’ facilities don’t have to understand specific routings, or issue flow measures for traffic unrelated.

For example, when issuing a mandatory or prohibited routing, it would be sensible on VATSIM for the adjacent 1 or 2 FIRs to be rerouting traffic on the ground, however beyond this, the traffic is much less relevant (within the window of this flow measure). With forward planning, such measures could be issued earlier (and end earlier) for further fields and then later for closer facilities. By issuing the exact same flow measure, but with different FAOs, this would be possible.


# Editing Flow Measures

‘Notified’ flow measures (i.e. not yet active) up to 30 minutes before they are activated can have any/all details edited. However, the system will simply delete this flow measure and re-issue a new one, with a new identifier, regardless of how small the change is.

For ‘Active’ flow measures and ‘Notified’ flow measures within 30 minutes of becoming activated, only certain values can be edited, specifically:

- end time
- reason box
- value of flow measure (e.g. 10NM or 5 minutes)
- existing filter types

To add a new FIR, filter type or change the start time, you must add a new filter. 

When you make the edit, the designator of the flow measure will change to include an appended number (e.g. EGTT06A will become EGTT06A-2) and notifications will be sent out via discord.

In general if you are expanding a flow measure to more traffic, consider simply issuing a NEW flow measure. This is preferred generally because controllers to whom the change is irrelevant do not receive cancellation notifications or change notifications of work they are already doing correctly. Editing is only really suitable to adjust existing parameters.

