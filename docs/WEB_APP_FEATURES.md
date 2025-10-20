# Web Application Features Specification

## Detailed Feature Requirements for NWRP Web Application

This document provides detailed specifications for each component of the Northumberland Warming Room Project web application.

---

## Feature 1: Winter Warming Room Location Finder

### Purpose
Interactive map-based tool to help individuals quickly find available warming rooms, emergency shelters, and related services in real-time.

### User Stories
- As a **person seeking shelter**, I want to quickly find the nearest open warming room so I can get out of the cold
- As a **community member**, I want to direct someone to available services so I can help someone in need
- As a **outreach worker**, I want to see all available spaces so I can make appropriate referrals

### Features & Requirements

#### 1.1 Interactive Map Display
- **Map Integration**: Leaflet.js with OpenStreetMap or Google Maps API
- **Current Location**: GPS-based "Find Me" functionality
- **Markers**: Clear icons indicating warming rooms, shelters, drop-ins
- **Clustering**: Group nearby locations when zoomed out
- **Custom Icons**: Visual indicators for status (open/closed/full)

**Visual Requirements**:
```
Map Elements:
🟢 Green marker = Open with availability
🟡 Yellow marker = Open but near capacity
🔴 Red marker = Closed or full
📍 Blue marker = Other services (food, medical)
⭐ Star badge = Currently at this location
```

#### 1.2 Location Details Panel
- **Facility Name**: Official name
- **Address**: Full street address with postal code
- **Status**: Real-time open/closed/capacity
- **Hours**: Operating hours
- **Services**: List of available services
- **Contact**: Phone number, email if available
- **Capacity**: Current occupancy and total capacity
- **Restrictions**: Age, gender, pets, family policies
- **Accessibility**: Wheelchair access, language support
- **Last Updated**: Timestamp of status update

#### 1.3 Directions & Navigation
- **Get Directions**: One-click navigation
- **Transit Routes**: Public transportation options
- **Walking Directions**: Pedestrian routes
- **Driving Directions**: For outreach workers
- **Distance Display**: Miles/kilometers from current location
- **Estimated Time**: Travel time by different modes

#### 1.4 Filtering & Search
- **Service Type Filter**: Warming room, shelter, drop-in, food
- **Availability Filter**: Open now, accepting new guests
- **Accessibility Filter**: Wheelchair accessible, language-specific
- **Population Filter**: Men, women, families, youth, LGBTQ2S+, pets
- **Search Bar**: Search by name, address, or postal code

#### 1.5 Offline Functionality
- **Offline Maps**: Downloadable map tiles
- **Cached Locations**: Last known locations available offline
- **Sync on Connect**: Update when internet available
- **Offline Notice**: Clear indication of offline mode

### Technical Specifications

**API Endpoints**:
```javascript
GET /api/locations
- Returns: Array of all locations with current status

GET /api/locations/{id}
- Returns: Detailed information for specific location

GET /api/locations/nearby?lat={lat}&lng={lng}&radius={km}
- Returns: Locations within specified radius

GET /api/locations/search?q={query}
- Returns: Locations matching search query

GET /api/locations/status
- Returns: Real-time status for all locations
```

**Data Model**:
```javascript
{
  "id": "uuid",
  "name": "Community Center Warming Room",
  "type": "warming_room",
  "address": {
    "street": "123 Main Street",
    "city": "Cobourg",
    "province": "Ontario",
    "postalCode": "K9A 1A1"
  },
  "coordinates": {
    "lat": 43.9597,
    "lng": -78.1674
  },
  "status": {
    "isOpen": true,
    "capacity": 25,
    "currentOccupancy": 18,
    "lastUpdated": "2025-10-20T20:30:00Z"
  },
  "hours": {
    "monday": "18:00-08:00",
    "tuesday": "18:00-08:00",
    // ... other days
  },
  "services": [
    "overnight_shelter",
    "hot_meals",
    "showers",
    "laundry"
  ],
  "restrictions": {
    "ageMin": 18,
    "acceptsPets": false,
    "acceptsFamilies": true
  },
  "accessibility": {
    "wheelchairAccessible": true,
    "languages": ["en", "fr"]
  },
  "contact": {
    "phone": "555-123-4567",
    "email": "info@example.org"
  }
}
```

---

## Feature 2: Proposal Generator & Planning Tool

### Purpose
Step-by-step wizard to help community organizations create professional warming room proposals for funding, location agreements, and partnerships.

### User Stories
- As a **community organizer**, I want to create a professional proposal so I can secure funding
- As a **municipal staff member**, I want a template for location agreements so I can standardize approvals
- As a **nonprofit director**, I want to calculate costs accurately so I can budget appropriately

### Features & Requirements

#### 2.1 Proposal Type Selection
- New warming room establishment
- Funding application (grants)
- Location partnership agreement
- Service expansion proposal
- Equipment request
- Volunteer program proposal

#### 2.2 Step-by-Step Wizard

**Step 1: Community Assessment**
- Population estimate input
- Geographic coverage map
- Current services inventory
- Needs gap analysis
- Demographic data
- Weather pattern analysis

**Step 2: Service Model Design**
- Operating schedule (nightly vs. activation-based)
- Hours of operation
- Target capacity
- Service offerings checklist
- Staffing model
- Volunteer requirements

**Step 3: Budget Builder**
- **Startup Costs Calculator**:
  - Facility deposits
  - Equipment purchases
  - Safety gear
  - Initial supplies
  - Insurance premiums
  - Legal fees
  - Marketing materials

- **Operating Budget Calculator**:
  - Monthly facility costs
  - Utilities (electricity, water, heating)
  - Supplies (food, hygiene, bedding)
  - Insurance (annual)
  - Training programs
  - Communications
  - Contingency fund (automatic 10%)

- **Revenue Projections**:
  - Government grants (municipal, provincial, federal)
  - Foundation grants
  - Corporate sponsorships
  - Individual donations
  - Fundraising events
  - In-kind donations (monetary value)

**Step 4: Location Details**
- Address and property information
- Accessibility assessment checklist
- Safety evaluation
- Capacity calculations
- Required improvements list
- Cost estimates for renovations

**Step 5: Implementation Timeline**
- Auto-generated Gantt chart
- Milestone setting
- Task assignments
- Critical path identification
- Timeline adjustments

**Step 6: Success Metrics**
- Output metrics selection
- Outcome goals setting
- Measurement frequency
- Reporting schedule
- Evaluation methods

**Step 7: Supporting Documents**
- Letters of support templates
- Partnership agreement templates
- Budget justification
- Risk assessment matrix
- Organizational capacity statement

#### 2.3 Template Library
- **Proposal Templates**:
  - Foundation grant applications
  - Municipal funding requests
  - Corporate sponsorship proposals
  - Location use agreements
  - Partnership MOUs

- **Supporting Documents**:
  - Budget templates
  - Timeline templates
  - Letters of support
  - Community needs assessment
  - Logic models

#### 2.4 Collaboration Features
- **Multi-user Editing**: Multiple stakeholders can contribute
- **Version Control**: Track changes and revisions
- **Comments & Feedback**: Inline commenting
- **Role-based Access**: View, edit, approve permissions
- **Approval Workflow**: Route for reviews

#### 2.5 Export & Formatting
- **Export Formats**:
  - PDF (professional formatting)
  - Microsoft Word (.docx)
  - Google Docs (direct export)
  - PowerPoint presentation
  - Plain text

- **Branding Options**:
  - Custom logos
  - Color schemes
  - Footer information
  - Organization branding

### Technical Specifications

**Wizard State Management**:
```javascript
{
  "proposalId": "uuid",
  "type": "new_warming_room",
  "currentStep": 3,
  "completedSteps": [1, 2],
  "data": {
    "communityAssessment": { /* step 1 data */ },
    "serviceModel": { /* step 2 data */ },
    "budget": { /* step 3 data */ }
  },
  "collaborators": [
    {
      "userId": "uuid",
      "role": "editor",
      "name": "Jane Smith"
    }
  ],
  "lastModified": "2025-10-20T15:30:00Z",
  "status": "draft"
}
```

**API Endpoints**:
```javascript
POST /api/proposals
- Create new proposal

GET /api/proposals/{id}
- Retrieve proposal

PUT /api/proposals/{id}
- Update proposal

GET /api/proposals/{id}/export?format=pdf
- Export proposal in specified format

GET /api/templates
- Get available templates

POST /api/proposals/{id}/collaborate
- Add collaborator
```

---

## Feature 3: Resource Directory & Service Navigator

### Purpose
Comprehensive, searchable database of social services, support programs, and community resources with real-time availability updates.

### User Stories
- As a **person seeking help**, I want to find services I'm eligible for so I can access support
- As a **case worker**, I want to make appropriate referrals so I can connect clients with services
- As a **volunteer**, I want accurate information so I can provide good guidance

### Features & Requirements

#### 3.1 Service Categories

**Primary Categories**:
- Emergency Services (shelter, crisis lines, emergency food)
- Housing Support (affordable housing, rent banks, housing navigators)
- Mental Health Services (counselling, psychiatry, peer support)
- Addiction Treatment (detox, rehab, harm reduction, recovery support)
- Healthcare (clinics, dental, vision, pharmacies)
- Food Programs (food banks, meal programs, community kitchens)
- Employment Services (job search, training, resume help)
- Financial Support (benefits, emergency funds, financial counselling)
- Legal Services (legal aid, tenant support, immigration)
- Education & Training (literacy, GED, college programs)
- Family Services (childcare, parenting, family counselling)
- Youth Services (youth shelters, programs, mentorship)
- Senior Services (housing, support, activities)
- Transportation (transit passes, ride programs)
- Personal Care (hygiene supplies, laundry, showers)

#### 3.2 Service Listings

**Each Service Entry Contains**:
- Organization name
- Service name and description
- Address and map location
- Hours of operation
- Contact information (phone, email, website)
- Eligibility requirements
- Required documents
- Cost/fees (if any)
- Languages supported
- Accessibility features
- Population served
- Service capacity/waitlist info
- Last verified date

#### 3.3 Search & Filter

**Search Functionality**:
- Keyword search (fuzzy matching)
- Category browse
- Location-based search (near me)
- Multi-criteria filtering

**Filters**:
- Service type
- Location/distance
- Open now/hours
- Cost (free, sliding scale, fee)
- Language
- Accessibility
- Population (age, gender, family status)
- Waitlist status

**Sort Options**:
- Distance (nearest first)
- Relevance (search match)
- Rating/reviews
- Recently updated
- Alphabetical

#### 3.4 Service Details Page

**Information Display**:
- Overview and description
- How to access/apply
- What to bring (documents needed)
- Eligibility criteria
- Process timeline
- Fees and funding options
- Staff and programs
- Reviews and ratings (optional)
- Related services
- Frequently asked questions

**Action Buttons**:
- Get Directions
- Call Now
- Email
- Visit Website
- Save/Bookmark
- Share
- Report Outdated Info

#### 3.5 Eligibility Checker

**Interactive Tool**:
- Question-based assessment
- Determines eligibility for programs
- Suggests matching services
- Explains why eligible/not eligible
- Provides alternative options

**Example Questions**:
- Age?
- Current housing situation?
- Income level?
- Family status?
- Health insurance?
- Legal status in Canada?

#### 3.6 User Features

**Account Features** (Optional):
- Save favorite services
- Track applied services
- Set reminders for appointments
- Notes for services
- Share list with case worker

**Offline Access**:
- Download service lists
- Save for offline viewing
- Sync when online

#### 3.7 Admin Features

**Service Management**:
- Add/edit/remove services
- Update availability
- Verify information
- Moderate reviews
- Track usage statistics
- Generate reports

**Data Quality**:
- Verification workflow
- Automated reminder to update
- Community reporting of errors
- Regular audits

### Technical Specifications

**Data Model**:
```javascript
{
  "serviceId": "uuid",
  "organizationId": "uuid",
  "name": "Emergency Food Bank",
  "description": "Provides emergency food hampers...",
  "category": ["emergency_services", "food_programs"],
  "subcategory": "food_bank",
  "address": {
    "street": "456 Oak Avenue",
    "city": "Cobourg",
    "province": "ON",
    "postalCode": "K9A 2B2"
  },
  "coordinates": {
    "lat": 43.9600,
    "lng": -78.1680
  },
  "hours": {
    "monday": "09:00-17:00",
    // ... other days
  },
  "contact": {
    "phone": "555-234-5678",
    "email": "info@foodbank.org",
    "website": "https://foodbank.org"
  },
  "eligibility": {
    "ageMin": null,
    "ageMax": null,
    "residencyRequired": true,
    "incomeLimit": 25000,
    "requiredDocuments": ["id", "proof_of_address"]
  },
  "accessibility": {
    "wheelchairAccessible": true,
    "languages": ["en", "fr"],
    "ttdAvailable": false
  },
  "cost": "free",
  "capacity": {
    "hasWaitlist": false,
    "currentWaitWeeks": 0
  },
  "lastVerified": "2025-10-15",
  "tags": ["emergency", "food", "family-friendly"],
  "statistics": {
    "views": 1245,
    "referrals": 89,
    "saves": 156
  }
}
```

**API Endpoints**:
```javascript
GET /api/services?category={cat}&location={lat,lng}&radius={km}
- Search services

GET /api/services/{id}
- Get service details

POST /api/services/{id}/check-eligibility
- Check eligibility based on criteria

GET /api/services/categories
- Get all service categories

POST /api/services/{id}/save
- Save service to user favorites

POST /api/services/{id}/report
- Report outdated or incorrect information
```

---

## Feature 4: Volunteer Management Portal

### Purpose
Centralized system for recruiting, scheduling, training, and managing warming room volunteers.

### User Stories
- As a **volunteer coordinator**, I want to easily schedule shifts so operations run smoothly
- As a **volunteer**, I want to see my schedule and sign up for shifts so I can contribute
- As an **administrator**, I want to track volunteer hours so I can report impact

### Features & Requirements

#### 4.1 Volunteer Registration

**Registration Form**:
- Personal information (name, email, phone)
- Address and emergency contact
- Availability (days/times)
- Skills and experience
- Languages spoken
- Special qualifications (first aid, medical, etc.)
- References
- Background check consent
- Waiver and agreement

**Onboarding Workflow**:
1. Submit application
2. Reference check
3. Background check
4. Orientation scheduling
5. Training completion
6. Activation as volunteer

#### 4.2 Shift Scheduling

**Shift Management**:
- Create shift templates
- Define roles per shift
- Set minimum/maximum volunteers
- Recurring shifts
- One-time special events
- Shift swap requests
- Emergency fill requests

**Volunteer Self-Scheduling**:
- View available shifts
- Sign up for open shifts
- Set availability preferences
- Request time off
- Swap shifts with approval
- Receive shift reminders

**Shift Types**:
- Evening (6:00 PM - 11:00 PM)
- Overnight (11:00 PM - 7:00 AM)
- Morning (7:00 AM - 8:00 AM)
- Setup/Cleanup
- Special events

#### 4.3 Training Management

**Training Modules**:
- Orientation (required)
- Safety and de-escalation (required)
- First aid and CPR (required)
- Trauma-informed care (required)
- Specialized training (optional)

**Training Tracking**:
- Completion status
- Certification expiry dates
- Renewal reminders
- Training history
- Continuing education credits

#### 4.4 Communication Tools

**Messaging**:
- Broadcast messages to all volunteers
- Shift-specific messages
- Direct messaging
- Emergency alerts
- Shift reminders (automated)
- Thank you messages

**Notification Channels**:
- Email
- SMS text
- In-app notifications
- Phone call (emergency)

#### 4.5 Hour Tracking & Reporting

**Time Tracking**:
- Check-in/check-out
- Automatic calculation
- Manual adjustment (with approval)
- Export timesheets
- Generate reports

**Recognition**:
- Hour milestones (25, 50, 100, 500, 1000)
- Volunteer of the month
- Service awards
- Thank you certificates
- Impact statistics

#### 4.6 Volunteer Dashboard

**Personal Dashboard**:
- Upcoming shifts
- Total hours contributed
- Training status
- Messages
- Shift feedback
- Impact statistics
- Recognition badges

### Technical Specifications

**Data Model**:
```javascript
{
  "volunteerId": "uuid",
  "profile": {
    "firstName": "John",
    "lastName": "Doe",
    "email": "john@example.com",
    "phone": "555-345-6789",
    "address": { /* address object */ }
  },
  "availability": {
    "monday": ["18:00-23:00"],
    "tuesday": ["18:00-23:00"],
    // ... other days
  },
  "skills": ["first_aid", "bilingual_french", "cooking"],
  "certifications": [
    {
      "type": "first_aid",
      "expiryDate": "2026-06-15",
      "verified": true
    }
  ],
  "trainingCompleted": [
    "orientation",
    "safety_deescalation",
    "first_aid_cpr"
  ],
  "status": "active",
  "hoursLogged": 127.5,
  "shiftsCompleted": 34,
  "lastShift": "2025-10-18"
}
```

---

## Feature 5: Admin Dashboard & Analytics

### Purpose
Comprehensive administrative interface for managing warming room operations, tracking metrics, and generating reports.

### User Stories
- As a **coordinator**, I want to see real-time operations so I can make informed decisions
- As a **funder**, I want to see impact metrics so I can evaluate program effectiveness
- As a **board member**, I want comprehensive reports so I can ensure accountability

### Features & Requirements

#### 5.1 Real-Time Operations Dashboard

**Live Metrics**:
- Current status (open/closed)
- Current occupancy vs. capacity
- Active volunteers on shift
- Tonight's forecast
- Incident alerts
- Supply levels
- Recent activity feed

**Quick Actions**:
- Activate/deactivate warming room
- Update occupancy
- Send notifications
- Contact volunteers
- View shift schedule
- Check inventory

#### 5.2 Statistics & Analytics

**Service Statistics**:
- Total individuals served (daily, weekly, monthly, seasonal)
- Total guest-nights
- Average occupancy rate
- Peak usage times
- Demographics (age ranges, gender if collected)
- First-time vs. returning guests
- Length of stay patterns

**Volunteer Metrics**:
- Total volunteers
- Active volunteers
- Total volunteer hours
- Hours by role/shift
- Retention rate
- No-show rate
- Average shifts per volunteer

**Service Delivery**:
- Meals served
- Showers provided
- Laundry loads
- Medical interventions
- Referrals made
- Supplies distributed

**Financial Tracking**:
- Budget vs. actual spending
- Cost per guest-night
- Funding sources
- Donation tracking
- In-kind contributions

#### 5.3 Reporting Tools

**Pre-built Reports**:
- Daily operations summary
- Weekly summary report
- Monthly performance report
- Seasonal impact report
- Annual report
- Funder-specific reports
- Custom date ranges

**Report Formats**:
- PDF export
- Excel spreadsheet
- CSV data export
- Interactive web report
- PowerPoint slides

**Report Components**:
- Executive summary
- Key statistics
- Visualizations (charts, graphs)
- Trends analysis
- Comparison to goals
- Success stories
- Recommendations

#### 5.4 Data Visualization

**Charts & Graphs**:
- Occupancy trends (line graph)
- Service utilization (bar chart)
- Demographics (pie chart)
- Volunteer hours (stacked bar)
- Budget breakdown (pie chart)
- Geographic distribution (map)
- Time series analysis

**Interactive Dashboards**:
- Filter by date range
- Drill-down capabilities
- Export visualizations
- Compare time periods
- Goal tracking

#### 5.5 System Administration

**User Management**:
- Add/remove users
- Role assignment
- Permission management
- Activity logging
- Password resets

**System Configuration**:
- Location settings
- Notification templates
- Activation thresholds
- Capacity limits
- Service offerings
- Integration settings

**Data Management**:
- Backup and restore
- Data export
- Archive old records
- Data retention policies
- Privacy compliance

### Technical Specifications

**Dashboard Data Structure**:
```javascript
{
  "realTimeMetrics": {
    "status": "open",
    "currentOccupancy": 18,
    "capacity": 25,
    "percentFull": 72,
    "volunteersOnShift": 3,
    "currentTemperature": -8,
    "lastUpdated": "2025-10-20T21:15:00Z"
  },
  "todayStatistics": {
    "checkIns": 23,
    "newGuests": 4,
    "mealsServed": 23,
    "incidents": 0
  },
  "seasonToDate": {
    "nightsOpen": 45,
    "totalGuestNights": 982,
    "uniqueIndividuals": 87,
    "volunteerHours": 675
  }
}
```

---

## Cross-Cutting Features

### Security & Privacy

**Authentication**:
- Email/password login
- Two-factor authentication (optional)
- Social login (Google, Facebook)
- Password recovery
- Session management

**Authorization**:
- Role-based access control (RBAC)
- Guest (public access)
- Volunteer (limited access)
- Coordinator (operations access)
- Administrator (full access)

**Data Privacy**:
- Minimal data collection
- Anonymization options
- Encrypted data storage
- Secure data transmission (SSL/TLS)
- PIPEDA compliance
- Right to be forgotten
- Data portability

### Accessibility (WCAG 2.1 AA)

**Requirements**:
- Keyboard navigation
- Screen reader compatibility
- Alt text for images
- Color contrast standards
- Resizable text
- Clear focus indicators
- Simple language
- Multi-language support

### Mobile Responsiveness

**Design Principles**:
- Mobile-first design
- Touch-friendly buttons
- Simplified navigation
- Offline functionality
- Reduced data usage
- Fast loading times
- Progressive Web App (PWA)

### Performance

**Targets**:
- Page load < 3 seconds
- Time to interactive < 5 seconds
- Smooth scrolling and transitions
- Efficient data caching
- Optimized images
- Lazy loading

### Notifications & Alerts

**Notification Types**:
- Warming room activation
- Shift reminders
- Capacity alerts
- Incident reports
- System updates
- Achievement recognition

**Delivery Channels**:
- Email
- SMS text
- Push notifications (PWA)
- In-app notifications
- Social media posts (automated)

---

## Implementation Priority

### Phase 1: MVP (Months 1-3)
1. ✅ Location Finder (basic map with manual updates)
2. ✅ Resource Directory (static database)
3. ✅ Basic volunteer scheduling
4. ✅ Simple admin dashboard

### Phase 2: Enhanced (Months 4-6)
1. ✅ Real-time status updates
2. ✅ Automated notifications
3. ✅ Proposal generator
4. ✅ Advanced volunteer features
5. ✅ Analytics and reporting

### Phase 3: Advanced (Months 7-12)
1. ✅ Case management tools
2. ✅ Community engagement platform
3. ✅ AI-powered recommendations
4. ✅ Advanced integrations
5. ✅ Mobile apps (iOS/Android)

---

## Success Metrics

**Usage Metrics**:
- Monthly active users
- Location finder searches
- Resource directory views
- Proposals generated
- Volunteer registrations

**Impact Metrics**:
- Individuals served
- Services connected
- Volunteer hours
- Funding secured
- Community reach

**Technical Metrics**:
- System uptime (>99%)
- Page load time (<3s)
- Error rate (<1%)
- User satisfaction (>4/5)

---

**Document Version**: 1.0  
**Last Updated**: October 2025  
**Contact**: NWRP Project Team  
**Repository**: https://github.com/acesonder/NWRP
