# Best Practice
Flow measures are designed to provide a traffic restriction to ease traffic congestion. A good flow measure should be targeted, proportional, practical and should balance restriction with network enjoyment.

### Targeted

A planned event does not necessarily need flow measures to balance traffic and likewise, poorly targeted flow measures will not necessarily balance traffic. By identifying or monitoring the traffic flow, restrictions can be placed on specific traffic streams to reduce workload.

When applying flow measures further away from your airspace, the longer until you will see any effect. It is bad practice to issue a flow measure applicable to controllers 3 hours away for an event that ends in 2 hours.

### Proportional

Flow measures apply extra work to controllers across the network. Therefore extreme measures like a groundstop, or flow measures applying to the whole or Europe for minor events should be avoided.

### Practical

Given the lack of global  24/7 coverage, application of flow measures to areas that are not staffed is impractical. It is encouraged that you request ATC in advance of an event for an area you think will be key to your flow strategy.

Likewise insisting that a neighbouring sector provides a high number of miles in trail for traffic inbound to you is only achievable if that area is well staffed. Flow measures should enable you to share the workload between controllers.

### Balanced

Flow measures are necessary, however keeping large delays in force to pilots disincentives them from flying again. Pilot and ATC enjoyment must be balanced.

# Create a Flow Measure

Flow measures created for a planned event **should** be linked to an event in the ECFMP system. They can be issued without, however it increases the awareness of adjacent controllers. Start by creating an event if one doesn’t already exist.

A flow measure is created on the system via the Flow Measures pages. You are required to have Flow Manager permissions for some FIRs in order to submit a measure. Any flow manager can submit any type of measure, however you must only issue restrictions that you understand and are competent to issue.

### Step 1
Select the event, if applicable. This will auto-populate the FIR field, which you must check.

Select the FIR. This is the FIR issuing the flow restriction and will restrict who can modify it. 

Start and End times are in UTC. They are when the flow measure will be in effect for. There is no facility to have an ‘indefinite’ measure, however end times can be modified. Therefore you should select the time you estimate the measure will need to last for. When in doubt, over-estimate the duration.

Reason should be provided for all flow measures, even if considered obvious. This message should specifically indicate the flow limitation (e.g. EDDH landing rate, Jever sector capacity). The presence of an event is not in itself a reason to require flow control.

# Types of Flow Measure

Every flow measure has a specific meaning and accepts a specific value:

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

### Miles in Trail (MIT)
A number of NM that aircraft must be presented in trail according to a specific criteria. This measure requires a ‘via’ waypoint at which the traffic should be in trail. Where multiple waypoints are specified, all traffic via any of these waypoints must be separated by this number of miles. Separate flow measures are therefore required where 2 separate traffic streams are to be separated in trail.

This method is ideal when adjacent area positions are well staffed, but only practical when the filters are simple. 

Traffic from different direction required to separately be X miles in trail requires multiple rules.

# Filters



