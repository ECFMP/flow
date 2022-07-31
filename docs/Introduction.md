# ECFMP

Welcome to the ECFMP (European Collaboration & Flow Management Project) Control system. From here, official flow measures are issued to the participating Divisions and vACCs by both discord notifications and via our system API.

This system acts as a partner to the ECFMP discord. You will need to have access to this discord and the relevant permissions **before** you are granted any permissions here. 

# System Permissions

The following permission levels exist:

### Normal User
Anyone that logs into ECFMP is able to view the list of events and flow measures that are active. This is intended so that areas that don’t have plugins or discord integration with us can still view the current network activity.

### Event Manager
Our flow measures are typically linked to events. Event Managers can create an event in advance and populate a list of participants. Flow measures can be created for participants and non-participants in events.

### Flow Manager
Flow Managers can issue Flow Measures via the system for the FIRs they represent. This provides a restriction to air traffic on the network and therefore requires a good understanding of the principles of the system and effect of measures on other parts of the network.

### Network Management Team
Members of the Network Management Team (NMT) are able to create ‘regional’ events and flow measures as well as grant permissions to other users.

# Questions and Errors

At the time of writing, this system is very new. We therefore expect there to be a few errors and plenty of questions as to how it works; however we would kindly request you familiarise with our documentation to see if your question can be answered there.

Questions can be directed into the #web-platform-questions channel in discord.

# Flight Information Regions

The FIR list within the system is taken from the list of FIRs published by Eurocontrol. Modifications to this list are only made at the request of the local FIR and agreement of the system team.

# Events
The events listed in ECFMP are not a comprehensive list of events around Europe. An event can be created whenever the scale of the event may require flow measures, or where the ECFMP API may be of use.

The “FIR” selected for the event merely defines the permissions as to who can edit the event after it is created. We recognise many events involve 2 vACCs, so please coordinate between you if it’s important who holds ownership of the event. Flow measures for an event can be issued by any FIR.

### Participants

Slotted events can **optionally** have a user list attached within ECFMP. This list is uploaded via the ‘import participants’ option, that accepts as CSV file of CIDs (VATSIM IDs). Uploading a CSV will overwrite all the existing data for the event.

The uploaded participant list can be accessed via the ECFMP API and used within plugins to identify event participants, or for use in flow measures issued for ‘member event’ and ‘member non-event’ filters. Note that these flow measure filters can be used without an ECFMP list of participants (see FM: Basics).

### VATCAN Code

The [VATCAN Event plugin](https://bookings.vatcan.ca/) is widely used. If in use for the above event, a code an be stored via ECFMP (available in our API) as a method for identifying event and non-event participants. A VATCAN code is optional.
