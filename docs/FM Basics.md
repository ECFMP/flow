# General Guidance
Flow measures are designed to provide a traffic restriction to ease traffic congestion. A good flow measure should be targeted, proportional, practical and should balance restriction with network enjoyment.

### Targeted

A planned event does not necessarily need flow measures to balance traffic and likewise, poorly targeted flow measures will not necessarily balance traffic. By identifying or monitoring the traffic flow, restrictions can be placed on specific traffic streams to reduce workload.

When applying flow measures further away from your airspace, the longer until you will see any effect. It is bad practice to issue a flow measure applicable to controllers 3 hours away for an event that ends in 2 hours.

### Proportional

Flow measures apply extra work to controllers across the network. Therefore extreme measures like a ground stop, or flow measures applying to the whole of Europe for minor events should be avoided.

### Practical

Given the lack of global  24/7 coverage, application of flow measures to areas that are not staffed is impractical. It is encouraged that you request ATC in advance of an event for an area you think will be key to your flow strategy.

Likewise insisting that a neighbouring sector provides a high number of miles in trail for traffic inbound to you is only achievable if that area is well staffed. Flow measures should enable you to share the workload between controllers.

### Balanced

Flow measures are necessary, however keeping large delays in force to pilots disincentives them from flying again. Pilot and ATC enjoyment must be balanced.

# Create a Flow Measure

Flow measures created for a planned event **should** be linked to an event in the ECFMP system. They can be issued without, however it increases the awareness of adjacent controllers. Start by creating an event if one doesn’t already exist.

A flow measure is created on the system via the Flow Measures pages. You are required to have Flow Manager permissions for some FIRs in order to submit a measure. Any flow manager can submit any type of measure, however you must only issue restrictions that you understand and are competent to issue.

### Walkthrough
Select the event, if applicable. This will auto-populate the FIR field, which you must check.

Select the FIR. This is the FIR issuing the flow restriction and will restrict who can modify it. 

Start and End times are in UTC. They are when the flow measure will be in effect for. There is no facility to have an ‘indefinite’ measure, however end times can be modified. Therefore you should select the time you estimate the measure will need to last for. When in doubt, over-estimate the duration.

Reason should be provided for all flow measures, even if considered obvious. This message should specifically indicate the flow limitation (e.g. EDDH landing rate, Jever sector capacity). The presence of an event is not in itself a reason to require flow control.

The flow measure required should then be selected. With the exception of ‘prohibit’ and 'ground stop' flow measures (see FM: Types), all measures require a value (e.g. a time, distance or waypoint) to be immediately specified. (see 'FM: Types' documentation for further details.

Flow measures then require filters and applicability information set in the ‘FAO’ tab. These are documented below.

Once complete, click ‘create’. A green box will confirm the flow measure is created - always check this box appears and handle any errors before closing the screen.


# Filters

Filters select which traffic a flow measure is applied to. The more specific you can be, the less restrictive you are being to network traffic. 

Where multiple different filter types are applied (e.g. ADEP, ADES, waypoint) the logic applied is **AND**. In other words, traffic matching this ADEP **AND** ADES **AND** waypoint.

Within a filter that accepts multiple values, the logic applied is  **OR**. For example providing 3 ADEP values (EGLL, EGKK, EGSS) will mean the restriction applies to traffic from EGLL  **OR** EGKK  **OR** EGSS. Likewise listing 3 different levels (FL310, 330 and 350) will mean this appliles to traffic at FL310 **OR**  FL330 **OR** FL350.



### ADEP and ADES
ADEP and ADES are mandatory filters referring to the departure and destination locations. For a specific airport, this should be an ICAO identifier. The default wildcard is \*\*\*\*, meaning applicability to any departure/destination and more specific wildcards can be used. For example: EI\*\* would apply to all airports in the Shannon FIR (Republic of Ireland). 

Airport groups are defined by the system team and/or the NMT. They are created as VATSIM-relevant groups of airports that flow measures might appropriately be applied to. 

Multiple values for ADEP and ADES are permitted. Please use this facility in preference to wildcards.

### Waypoint
This is a comma-separated list of waypoints (or short route strings) for which the flow measure applies to. For example:

> LOGAN

Would apply to all traffic routing via LOGAN.

> SASKI L608 LOGAN

Applies to traffic routing to LOGAN via SASKI. Where a pilot files the wrong airway, or some equivalent of SASKI DCT LOGAN, we would reasonably expect this to apply.

> SASKI L608 LOGAN, BARMI

As above, applies to traffic routing to LOGAN via SASKI **OR** traffic routing via BARMI.

### Level Above / Level Below

The “Level Above” and “Level Below” are separate, optional filters. “Level above” specifies traffic at the level specified and above. “Level below”, conversely traffic at the level specified and below. It is important to remember when adding multiple filters the logic is **AND**, not or, therefore using both filters must create a single block of levels.

Examples:

> Level above “270”

Traffic at FL270 or higher

> Level above “270” & Level below “300”

Traffic at or between FL270 and FL300.

> Level above “270” & Level below “200”

This is impossible. Traffic at FL270 or higher **AND** below FL200. To perform this action you would need multiple flow measures.

### Level

Filter only traffic at this specified level. This optional filter can be specified multiple times to allow the traffic to be at multiple levels.

“Level” can never be used with “Level above” or “Level below” as it cannot create possible combinations beyond “level” used in isolation.


### Member Event / Member non-Event

Allows the flow measures to be specific only to slotted members (or unslotted members). Requires the event in question to be within the ECFMP system.

Setting this parameter does not require a participant list to be set up for the event, though we suggest it if suitably planned in advance. The method to be used to determine should be coordinated by the event teams

### Range to Destination

Defines the applicability of a flow measure to be when within a certain distance from destination. This is primarily intended for airborne speed restrictions, whereby setting this filter defines the start point of the speed reduction. We do not recommend using this filter as a surrogate for defining appropriate ADEP locations.


# FAO
The FAO field requires you to select FIRs that you expect to implement the flow measure. Even if the flow measure is only relevant to one FIR by the filters you have selected, it won’t be appropriately propagated without a tag.

It is expected that filters will be used appropriately to improve the readability of flow measures published, rather than relying exclusively on the FAO tag. For example, to restrict all traffic that departs the Republic of Ireland, the ADEP field should be appropriately set in addition to the FAO tag for Shannon. This safeguards inexperienced users from misinterpreting the flow measure.

See FM: Advanced for examples of selective use of FAO tagging.
