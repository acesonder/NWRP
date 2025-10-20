# Warming Room Status Tool - Usage Guide

The Warming Room Status Tool is a command-line utility designed to help manage and communicate the operational status of the warming room facility.

## Overview

This tool allows coordinators, volunteers, and community members to:
- Check if the warming room is currently active
- Activate/deactivate the warming room with relevant information
- Track occupancy levels
- Update contact and location information

## Installation

### Requirements
- Python 3.6 or higher

### Setup
No installation required! The script is self-contained and can be run directly:

```bash
python warming_room_status.py --check
```

Or if executable permissions are set:
```bash
./warming_room_status.py --check
```

## Usage Examples

### Checking Current Status

To check if the warming room is currently active:

```bash
python warming_room_status.py --check
```

Example output:
```
============================================================
NORTHUMBERLAND WARMING ROOM STATUS
============================================================
🔴 STATUS: INACTIVE - The warming room is currently closed
------------------------------------------------------------
Contact: Contact community services for information
============================================================
```

### Activating the Warming Room

Activate with just a reason:
```bash
python warming_room_status.py --activate "Temperature forecast below -10°C"
```

Activate with full details:
```bash
python warming_room_status.py --activate "Extreme cold weather alert" \
  --duration "48 hours" \
  --location "123 Community Center St" \
  --capacity 30
```

### Deactivating the Warming Room

```bash
python warming_room_status.py --deactivate
```

### Updating Occupancy

As people check in and out, update the current occupancy:

```bash
python warming_room_status.py --occupancy 15
```

This helps track available space and capacity.

### Updating Information

Update contact information:
```bash
python warming_room_status.py --update-contact "Emergency: 555-1234"
```

Update location:
```bash
python warming_room_status.py --update-location "456 New Location St"
```

## Command Reference

### Options

| Option | Argument | Description |
|--------|----------|-------------|
| `--check` | None | Display current warming room status |
| `--activate` | REASON | Activate the warming room with specified reason |
| `--deactivate` | None | Deactivate the warming room |
| `--duration` | DURATION | Set expected duration (use with --activate) |
| `--location` | LOCATION | Set or update location |
| `--capacity` | NUMBER | Set maximum capacity (use with --activate) |
| `--occupancy` | NUMBER | Update current occupancy count |
| `--update-contact` | INFO | Update contact information |

## Data Storage

The tool stores status information in a local file: `warming_room_status.json`

This file contains:
- Current activation status (active/inactive)
- Last update timestamp
- Activation reason
- Expected duration
- Location
- Capacity and occupancy information
- Contact information

### Example status file:
```json
{
  "is_active": true,
  "last_updated": "2025-10-20 14:30:00",
  "activation_reason": "Temperature below -10°C",
  "expected_duration": "48 hours",
  "contact_info": "Emergency: 555-1234",
  "location": "123 Community Center St",
  "capacity": 30,
  "current_occupancy": 15
}
```

## Workflow Examples

### Opening for the Night

```bash
# Activate the warming room
python warming_room_status.py --activate "Temperature -12°C tonight" \
  --duration "Until 8am tomorrow" \
  --location "Community Center, 123 Main St" \
  --capacity 25

# Check status to confirm
python warming_room_status.py --check
```

### During Operation

```bash
# Update occupancy as people arrive
python warming_room_status.py --occupancy 10

# Later, update again
python warming_room_status.py --occupancy 18

# Check status at any time
python warming_room_status.py --check
```

### Closing in the Morning

```bash
# Deactivate when closing
python warming_room_status.py --deactivate

# Verify status
python warming_room_status.py --check
```

## Integration Ideas

### Automated Notifications

The status file (`warming_room_status.json`) can be:
- Read by web applications to display current status
- Monitored by notification services to send alerts
- Integrated with social media posting tools
- Used by chatbots to answer status queries

### Web Dashboard

Create a simple web page that reads the JSON file and displays:
- Current status (open/closed)
- Location and directions
- Available capacity
- Contact information

### SMS/Email Alerts

Set up a script that:
1. Monitors the status file for changes
2. Sends notifications when status changes
3. Alerts registered users when the room opens

## Best Practices

1. **Always update when opening/closing**: Use `--activate` and `--deactivate` to keep status current

2. **Keep occupancy current**: Update occupancy regularly to help manage capacity

3. **Provide clear reasons**: When activating, include specific reason (temperature, weather alert, etc.)

4. **Set realistic durations**: Help people plan by indicating expected duration

5. **Update contact info**: Ensure contact information is always current and accurate

6. **Regular status checks**: Periodically verify status matches actual operation

## Troubleshooting

### Permission Denied
If you get a permission error, make sure the script is executable:
```bash
chmod +x warming_room_status.py
```

### File Not Found
The tool creates `warming_room_status.json` automatically on first run. If you want to reset:
```bash
rm warming_room_status.json
python warming_room_status.py --check
```

### Python Not Found
Ensure Python 3 is installed:
```bash
python3 --version
```

If Python 3 is installed as `python3`, use that instead:
```bash
python3 warming_room_status.py --check
```

## Contributing

Have ideas for improving this tool? See [CONTRIBUTING.md](../CONTRIBUTING.md) for how to contribute!

Possible enhancements:
- Weather API integration for automatic activation
- Email/SMS notification system
- Web dashboard interface
- Mobile app integration
- Multi-language support
- Historical tracking and reporting

---

*For questions or support, please open an issue on GitHub.*
