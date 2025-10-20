# Quick Start Guide

Welcome to the Northumberland Warming Room Proposal (NWRP) repository! This guide will help you get started quickly.

## What is NWRP?

NWRP is a collaborative project for planning and managing the Northumberland warming room initiative - a critical community service providing shelter during cold weather.

## Getting Started in 3 Steps

### 1. 📖 Read the Documentation

Start with these key documents:
- **[README.md](README.md)** - Project overview
- **[BRAINSTORMING.md](BRAINSTORMING.md)** - Current ideas and proposals
- **[CONTRIBUTING.md](CONTRIBUTING.md)** - How to participate

### 2. 💡 Share Your Ideas

Have ideas for improving the warming room? You can:
- Add quick ideas to [BRAINSTORMING.md](BRAINSTORMING.md)
- Create detailed proposals in the [proposals/](proposals/) directory
- Check out the [example proposal](proposals/volunteer-coordination-system.md)

### 3. 🛠️ Use the Tools

Try the warming room status tool:

```bash
# Check if the warming room is active
python warming_room_status.py --check

# Activate the warming room
python warming_room_status.py --activate "Cold weather alert" \
  --location "Community Center" --capacity 25

# Update occupancy
python warming_room_status.py --occupancy 10
```

See [docs/TOOL_USAGE.md](docs/TOOL_USAGE.md) for complete documentation.

## Key Features

### 📝 Brainstorming System
Organized sections for:
- Operational ideas (activation criteria, facilities, staffing)
- Service delivery (basic and enhanced services)
- Technology & tools
- Funding & sustainability
- Community partnerships
- Safety & security

### 🤝 Collaboration Framework
Clear guidelines for:
- Contributing ideas
- Providing feedback
- Making changes
- Code of conduct

### 🔧 Utility Tool
Command-line tool for:
- Checking warming room status
- Activating/deactivating operations
- Tracking occupancy
- Managing information

### 📋 Proposal System
Structured templates for:
- Detailed proposals
- Implementation plans
- Resource requirements
- Success metrics

## Example Workflow

### For Coordinators

1. **Activation Needed**
   ```bash
   python warming_room_status.py --activate "Temperature -12°C" \
     --duration "24 hours" --location "123 Main St" --capacity 25
   ```

2. **During Operation**
   ```bash
   # Update as people arrive
   python warming_room_status.py --occupancy 15
   
   # Check current status
   python warming_room_status.py --check
   ```

3. **Closing**
   ```bash
   python warming_room_status.py --deactivate
   ```

### For Contributors

1. **Review existing ideas** in [BRAINSTORMING.md](BRAINSTORMING.md)

2. **Add your ideas** by editing the document or creating a proposal

3. **Submit changes** via pull request

4. **Engage in discussion** through GitHub issues and comments

## Need Help?

- 📚 Read the [documentation](docs/TOOL_USAGE.md)
- 💬 Open a GitHub issue with questions
- 🤝 See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines

## What's Included

```
NWRP/
├── README.md                    # Project overview
├── QUICK_START.md              # This file
├── BRAINSTORMING.md            # Ideas and proposals
├── CONTRIBUTING.md             # Contribution guidelines
├── warming_room_status.py      # Status management tool
├── docs/
│   └── TOOL_USAGE.md          # Detailed tool documentation
└── proposals/
    ├── README.md              # Proposal guidelines
    └── volunteer-coordination-system.md  # Example proposal
```

## Next Steps

1. ✅ Explore the repository structure
2. ✅ Try the warming room status tool
3. ✅ Review existing brainstorming ideas
4. ✅ Consider what you can contribute
5. ✅ Start collaborating!

---

**Questions?** Open an issue or check the documentation!
