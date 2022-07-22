# Common Issues

### Filters: AND / OR Logic

The AND/OR logic of flow measure filters is fixed and intended to be simple enough for both Flow Managers and API developers to work with. Complex and conditional logic is avoided.

Consider each named filter a ‘type’ e.g. the ADEP type. You can have multiple filters of the type ADEP. Where filters are of the same type in a single flow measure, **OR** logic applies, meaning for the flight to match it must meet one of the criteria (e.g. ADEP is EGLL **OR** EGKK, then flights from either airport match the flow measure). Between each filter type, **AND** logic applies, meaning for the flight to match it must meet all criteria (e.g. if ADEP is “EGLL” and waypoint is “BPK”, it must be a EGLL departure **AND** fly via BPK).

Example:
> ADEP: EGLL, EGKK, EGSS
> 
> waypoint: BPK, CLN, FRANE, REDFA
> 
> Level below: FL300

Refers to traffic departing:
- any of EGLL or EGKK or EGSS
- routing via one of BPK or CLN or FRANE or REDFA
- cruising/transferred at FL300 or below

### Departure Flow Measures - multiple airports

Please read the AND/OR logic (above) first.

When attempting to apply departure flow measures (i.e. MDI, ADI, RpH), issuing multiple airports in ADEP results in 'OR' logic - in other words that your flow measure has to be coordinated across those airports to achieve the number created. This is possible when planning in advance, such that a flow coordinator can coordinate and manage the traffic requesting to departure. Without prior coordination with the local flow managers however, this is impossible to achieve. 

Tower controllers are **never** expected to coordinate MDIs with other airports unless agreed well in advance by local flow teams.

Where a departure flow measure is intended to apply **separately** to multiple airports, a flow measure is required for each. Use the copy function to create mutliple flow measures if required.

# Example Specific Measures

### Ground Stop

A ground stop (i.e. stopping ground traffic departing to an airport) is considered an emergency measure. When issuing an unplanned ground stop, we suggest a time period of 30 minutes (1 hour maximum) with a review each hour. These measures are highly restrictive and should not be applied without careful thought.

### Re-route from point to point

Where you wish for all traffic to be re-routed from one point/route to another point/route, use the ‘mandatory route’ measure in isolation. Issue the mandatory route via a new waypoint/route, with a filter of the old (incorrect) waypoint/route. It is helpful to include route strings in the ‘reason’ field to assist controllers making the re-route.

When deciding who should be issuing this re-route, consider distance from your airport and use the FAO to include only close FIRs. Ground stations receiving the re-route will be expected to enforce the re-route regardless. Airborne traffic are expected to be re-routed by the FIR where the start of the re-route occurs.

For the example of re-routing traffic to Dublin (EIDW) away from “ABLIN” and instead to “VATRY”.

> Type: Mandatory Route
> 
> Value: 
> 
> Mandatory Route: VATRY
> 
> ADEP: \*\*\*\*
> 
> ADES: EIDW
> 
> Waypoint: ABLIN
> 
> FAO: London, Brest, Paris, Brussels

### Re-route without preference

Where a particular route needs to be avoided, however there are either multiple re-route options, or the re-route choice does not matter, use a ‘Prohibit’ measure. 

For the example of an event with slot bookings between airport XXXX and YYYY whereby all traffic will be routing via ‘ABCDE’, it may be preferable for traffic without a slot to take an alternate route.

> Type: Prohibit
> 
> Value: 
> 
> ADEP: XXXX
> 
> ADES: YYYY
> 
> Waypoint: ABCDE
> 
> Member Not Event: ‘My Event Name’
> 
> FAO: FIR Name

### Limit route options

To limit route options (e.g. for non-event/event traffic), you can either prohibit undesirable options, or send a mandatory route instruction for the valid options. We would suggest the following good practice

- if limiting routes from all directions, create multiple flow measures to make the routes seen by controllers relevant to them
- providing explanation and re-route strings is helpful to less experienced controllers
- use the fewest number of route strings to achieve the purpose (i.e. don’t submit 6 mandatory routes, where 1 prohibit would have achieved the same result
- try to stick to either prohibit OR mandatory route measures due to the possibility of conflicting instructions being issued

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

