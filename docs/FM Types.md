Every flow measure has a specific meaning and accepts a specific value. You must understand the required/suggested values before applying a flow measure. If in doubt, ask.

## Departing Traffic

### Minimum Departure Interval (MDI)
A minimum time between which aircraft are permitted to depart. Once an aircraft subject to this restriction departs, another is not permitted until the end of the specified time. 

This is a very specific and restrictive measure and should be targeted to areas where high volumes of traffic will produce congestion in a specific airspace. If attempting to more ‘generally’ restrict excessive departures, consider a rate per hour, or average departure interval.

### Average Departure Interval (ADI)
The average time between which aircraft are permitted to depart, averaged over 3 departures. 

For the example of an ADI of 10 minutes: departures 1 and 2 could be departed 3 minutes apart, however the next departure would then have to wait 17 minutes until it was allowed airborne. 

Likewise for an ADI of 10 minutes, departure 2 could be airborne 18 minutes after departure 1, meaning departure 3 could be airborne after only 2 minutes. The 4th departure however would then have to wait a further 16 minutes.

### Rate Per Hour
The number of flights permitted fitting a condition per hour. This can be applied to departures, or traffic via a certain route. 

This method is highly effective at restricting traffic volume. It is much more practical to issue to traffic on the ground. Activating for airborne traffic may lead to prolonged enroute holding and sudden traffic ‘dumps’ when the hour is expired. Consider a MIT restriction for airborne traffic.

## Miles in Trail (MIT)
A number of NM that aircraft must be presented in trail according to a specific criteria. This method is ideal when adjacent area positions are well staffed, but only practical when the filters are simple. Traffic from different direction required to separately be X miles in trail requires multiple rules.

A Miles In Trail requirement is applied by the FIRs that are tagged into the flow measure and expected to be implemented by the point of transfer to the issuing FIR. Ideally every MIT requirement should have some ‘via’ waypoints specified to avoid it accidentally applying to traffic the issuing FIR was not expecting. Where multiple waypoints are specified, the MIT requirement applies to all the traffic meeting the criteria (i.e. traffic via both waypoints). Where separate MIT requirements are required for each waypoint, multiple flow measures are required.

#### Example MIT 1
>Issued by: EGTT
>Tagged: EHAA
>Value: 15NM
>ADEP: ****
>ADES: EGLL
>Waypoints: NOGRO, ABNED

This measure would limit transfer of traffic from Amsterdam to London to be 15NM in trail if it is going to Heathrow via NOGRO and ABNED (which should be all traffic). If 1 aircraft is going to NOGRO and the other to ABNED, they still must be 15NM behind each other at transfer.

#### Example MIT 2
>Issued by: EGTT
>Tagged: EHAA
>Value: 15NM
>ADEP: ****
>ADES: ****
>Waypoints:

This is less well written. If issued, it would require 15NM for all traffic on any route from EHAA to London. This measure should include either specific waypoints, a more specific destination list or some other optional filters.

## Speed-based flow measures

All speed based flow measures should be issued with a “level above” or “level below” depending on whether being issued as IAS or Mach. By default an IAS flow measure should include a level below of FL270 and a Mach flow measure level above FL280. If this is forgotten when issuing, controllers are not expected to provide IAS reductions to high level traffic and vice versa. 

Speed-based flow measures only apply to the FIRs tagged in the ‘FAO’ section, even if the flow measures match the criteria. 

Speed-based flow measures are to be applied before the transfer of control to the issuing FIR, unless a ‘Range to Destination’ filter is added. When such a filter is added, speed reduction should be applied as soon as possible from this range.

### Max IAS
A maximum Indicated Airspeed that aircraft should be transferred on. Unless all transfers occur below FL270, it would be typical to issue a Maximum Mach measure in addition.

Where an IAS reduction puts an aircraft below their minimum clean speed (outside the terminal environment), the minimum clean speed shall be issued instead.

The above introduction to ‘speed-based flow measures’ outlines additional important rules.

### Max Mach
A maximum Mach that aircraft should be transferred on. 

Where the Maximum Mach is above the minimum Mach for an aircraft to remain in clean configuration, the pilot shall be instructed to maintain the lowest suitable Mach number.

The above introduction to ‘speed-based flow measures’ outlines additional important rules.


### IAS Reduction 
An IAS reduction may be used in conjunction with a Mach reduction flow measure to slow traffic inbound to an area of congestion. A maximum IAS may be useful/sensible instead when only attempting to apply restriction to traffic descending to land.

Where an IAS reduction puts an aircraft below their minimum clean speed (outside the terminal environment), the minimum clean speed shall be issued instead.

The above introduction to ‘speed-based flow measures’ outlines additional important rules.

### Mach Reduction
A Mach reduction is typically used to slow traffic far from destination to ease congestion. This measure is suggested to be used at distances of 200-400NM from destination to have a meaningful impact. 

Where the Maximum Mach is above the minimum Mach for an aircraft to remain in clean configuration, the pilot shall be instructed to maintain the lowest suitable Mach number.

The above introduction to ‘speed-based flow measures’ outlines additional important rules.

## Route Restrictions
Route restrictions are complex flow measures to be implemented. A poorly constructed or poorly communicated route restriction is likely to create significant confusion and workload. We suggest that route restrictions are discussed with adjacent flow managers before issued and as such it should be rare for such a measure to be active immediately after issue.

These measures do not have a ‘value’ associated with them like the prior flow measures. These measures should be issued with a waypoint

A route restriction when issued applies to traffic on the ground and in the air from the time of issue. Therefore it must be possible for both types of traffic to be re-routed by controllers as we do not have universal coverage. Providing suitable supplementary information (i.e. re-route strings) will significantly increase the likelihood of successful re-routing.

Combinations of “prohibit”, “mandatory route” and “MIT” flow measures can be used to ‘Compound Restrictions’. This will be elaborated in Flow Measures 2.

### Prohibit
The prohibit function is designed to allow you to selectively re-route traffic away from a busy route, or prevent a less favourable route option for the current online ATC. For any prohibit option, a suitable re-route suggestion should be provided even if it appears immediately obvious, as those implementing the restriction could be ground controllers at distant airfields.

The prohibit rule should not be used for Ground Stops and must therefore should always have a suitable filter (e.g. waypoint or flight level).

### Mandatory Route
Where planning of traffic via specific routes is convenient (e.g. to avoid unstaffed sectors during events), a mandatory route may be issued. Multiple mandatory routes can be issued within the same flow measures (i.e. alternatives) through the ‘add to mandatory route’ option. Each route should be the entire section of mandatory route with additional mandatory routes added as alternatives. For example:

> BCN

Would require traffic to file/route via BCN. (Note that BCN does not have to be in the flightplan string as long as it is included on an airway)

> BCN Q63 STU M17 VATRY 

>BCN Q63 STU M456 BAKUR

Would allow traffic to route via BCN and then STU to VATRY and BAKUR only

> BAKUR

> VATRY

Would allow traffic to route to BAKUR or VATRY. This could be via BCN or another valid route.

