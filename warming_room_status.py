#!/usr/bin/env python3
"""
Warming Room Status Tool

A utility for managing and checking the activation status of the warming room.
This tool helps coordinators and volunteers stay informed about when the warming
room is activated and provides helpful information.
"""

import argparse
import json
import sys
from datetime import datetime
from pathlib import Path
from typing import Dict, Optional


class WarmingRoomStatus:
    """Manage warming room activation status."""
    
    STATUS_FILE = Path("warming_room_status.json")
    
    def __init__(self):
        """Initialize the warming room status manager."""
        self.status_data = self._load_status()
    
    def _load_status(self) -> Dict:
        """Load status from file or create default."""
        if self.STATUS_FILE.exists():
            with open(self.STATUS_FILE, 'r') as f:
                return json.load(f)
        return {
            "is_active": False,
            "last_updated": None,
            "activation_reason": None,
            "expected_duration": None,
            "contact_info": "Contact community services for information",
            "location": "To be determined",
            "capacity": 0,
            "current_occupancy": 0
        }
    
    def _save_status(self) -> None:
        """Save current status to file."""
        with open(self.STATUS_FILE, 'w') as f:
            json.dump(self.status_data, f, indent=2)
    
    def check_status(self) -> None:
        """Display current warming room status."""
        print("\n" + "="*60)
        print("NORTHUMBERLAND WARMING ROOM STATUS")
        print("="*60)
        
        if self.status_data["is_active"]:
            print("🟢 STATUS: ACTIVE - The warming room is currently open")
        else:
            print("🔴 STATUS: INACTIVE - The warming room is currently closed")
        
        print("-"*60)
        
        if self.status_data["last_updated"]:
            print(f"Last Updated: {self.status_data['last_updated']}")
        
        if self.status_data["is_active"]:
            if self.status_data["activation_reason"]:
                print(f"Reason: {self.status_data['activation_reason']}")
            
            if self.status_data["expected_duration"]:
                print(f"Expected Duration: {self.status_data['expected_duration']}")
            
            if self.status_data["location"]:
                print(f"Location: {self.status_data['location']}")
            
            if self.status_data.get("capacity", 0) > 0:
                capacity = self.status_data["capacity"]
                occupancy = self.status_data.get("current_occupancy", 0)
                available = capacity - occupancy
                print(f"Capacity: {occupancy}/{capacity} (Available: {available})")
        
        print(f"Contact: {self.status_data['contact_info']}")
        print("="*60 + "\n")
    
    def activate(self, reason: str, duration: Optional[str] = None,
                 location: Optional[str] = None, capacity: Optional[int] = None) -> None:
        """Activate the warming room."""
        self.status_data["is_active"] = True
        self.status_data["last_updated"] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        self.status_data["activation_reason"] = reason
        
        if duration:
            self.status_data["expected_duration"] = duration
        if location:
            self.status_data["location"] = location
        if capacity is not None:
            self.status_data["capacity"] = capacity
            self.status_data["current_occupancy"] = 0
        
        self._save_status()
        print("\n✅ Warming room has been ACTIVATED")
        self.check_status()
    
    def deactivate(self) -> None:
        """Deactivate the warming room."""
        self.status_data["is_active"] = False
        self.status_data["last_updated"] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        self.status_data["activation_reason"] = None
        self.status_data["expected_duration"] = None
        self.status_data["current_occupancy"] = 0
        
        self._save_status()
        print("\n⚠️  Warming room has been DEACTIVATED")
        self.check_status()
    
    def update_occupancy(self, count: int) -> None:
        """Update current occupancy count."""
        if not self.status_data["is_active"]:
            print("⚠️  Warning: Warming room is not currently active")
            return
        
        capacity = self.status_data.get("capacity", 0)
        if capacity > 0 and count > capacity:
            print(f"⚠️  Warning: Occupancy ({count}) exceeds capacity ({capacity})")
        
        self.status_data["current_occupancy"] = count
        self.status_data["last_updated"] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        self._save_status()
        
        print(f"\n✅ Occupancy updated to {count}")
        if capacity > 0:
            available = capacity - count
            print(f"Available spaces: {available}")
    
    def update_info(self, contact: Optional[str] = None,
                    location: Optional[str] = None) -> None:
        """Update warming room information."""
        if contact:
            self.status_data["contact_info"] = contact
        if location:
            self.status_data["location"] = location
        
        self.status_data["last_updated"] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        self._save_status()
        print("\n✅ Information updated successfully")


def main():
    """Main entry point for the warming room status tool."""
    parser = argparse.ArgumentParser(
        description="Warming Room Status Management Tool",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  %(prog)s --check                          Check current status
  %(prog)s --activate "Temperature below -10°C" --duration "48 hours"
  %(prog)s --activate "Cold weather alert" --location "123 Main St" --capacity 30
  %(prog)s --deactivate                     Deactivate the warming room
  %(prog)s --occupancy 15                   Update occupancy count
  %(prog)s --update-contact "555-1234"      Update contact information
        """
    )
    
    parser.add_argument('--check', action='store_true',
                       help='Check current warming room status')
    parser.add_argument('--activate', metavar='REASON',
                       help='Activate warming room with reason')
    parser.add_argument('--deactivate', action='store_true',
                       help='Deactivate the warming room')
    parser.add_argument('--duration', metavar='DURATION',
                       help='Expected duration of activation')
    parser.add_argument('--location', metavar='LOCATION',
                       help='Location of warming room')
    parser.add_argument('--capacity', type=int, metavar='N',
                       help='Maximum capacity')
    parser.add_argument('--occupancy', type=int, metavar='N',
                       help='Update current occupancy count')
    parser.add_argument('--update-contact', metavar='INFO',
                       help='Update contact information')
    
    args = parser.parse_args()
    
    # If no arguments provided, show help
    if len(sys.argv) == 1:
        parser.print_help()
        sys.exit(0)
    
    manager = WarmingRoomStatus()
    
    if args.activate:
        manager.activate(
            reason=args.activate,
            duration=args.duration,
            location=args.location,
            capacity=args.capacity
        )
    elif args.deactivate:
        manager.deactivate()
    elif args.occupancy is not None:
        manager.update_occupancy(args.occupancy)
    elif args.update_contact or args.location:
        manager.update_info(
            contact=args.update_contact,
            location=args.location
        )
    elif args.check:
        manager.check_status()
    else:
        # Default to checking status
        manager.check_status()


if __name__ == "__main__":
    main()
