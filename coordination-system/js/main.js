/**
 * Volunteer Coordination System - Main JavaScript
 * Handles frontend functionality and API interactions
 */

class VolunteerApp {
    constructor() {
        this.apiBase = 'api/';
        this.currentSection = 'dashboard';
        this.volunteers = [];
        this.shifts = [];
        this.activations = [];
        
        this.init();
    }

    async init() {
        this.setupEventListeners();
        this.setupNavigation();
        await this.loadDashboard();
    }

    setupEventListeners() {
        // Navigation
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const section = e.target.dataset.section;
                this.navigateToSection(section);
            });
        });

        // Form submissions
        const messageForm = document.getElementById('message-form');
        if (messageForm) {
            messageForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.sendMessage();
            });
        }

        // Modal close
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                this.closeModal();
            }
        });
    }

    setupNavigation() {
        // Set initial active section
        this.navigateToSection('dashboard');
    }

    navigateToSection(section) {
        // Update nav active state
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });
        document.querySelector(`[data-section="${section}"]`).classList.add('active');

        // Show/hide content sections
        document.querySelectorAll('.content-section').forEach(sec => {
            sec.classList.remove('active');
        });
        document.getElementById(`${section}-section`).classList.add('active');

        this.currentSection = section;

        // Load section data
        switch (section) {
            case 'dashboard':
                this.loadDashboard();
                break;
            case 'volunteers':
                this.loadVolunteers();
                break;
            case 'scheduling':
                this.loadScheduling();
                break;
            case 'communications':
                this.loadCommunications();
                break;
            case 'reports':
                this.loadReports();
                break;
        }
    }

    // API Methods
    async apiRequest(endpoint, method = 'GET', data = null) {
        try {
            this.showLoading();
            
            const options = {
                method,
                headers: {
                    'Content-Type': 'application/json',
                }
            };

            if (data && method !== 'GET') {
                options.body = JSON.stringify(data);
            }

            const response = await fetch(this.apiBase + endpoint, options);
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'API request failed');
            }

            return result;
        } catch (error) {
            this.showToast('Error: ' + error.message, 'error');
            throw error;
        } finally {
            this.hideLoading();
        }
    }

    // Dashboard Methods
    async loadDashboard() {
        try {
            // Load statistics
            const volunteers = await this.apiRequest('volunteers.php?status=active');
            const shifts = await this.apiRequest('shifts.php?upcoming=1');
            
            document.getElementById('active-volunteers').textContent = volunteers.length;
            document.getElementById('upcoming-shifts').textContent = shifts.length;
            
            // Calculate coverage rate
            const filledShifts = shifts.filter(s => s.confirmed_volunteers >= s.min_volunteers);
            const coverageRate = shifts.length > 0 ? Math.round((filledShifts.length / shifts.length) * 100) : 0;
            document.getElementById('coverage-rate').textContent = coverageRate + '%';
            
            const unfilledShifts = shifts.length - filledShifts.length;
            document.getElementById('unfilled-shifts').textContent = unfilledShifts;
            
            // Load upcoming shifts
            this.displayUpcomingShifts(shifts.slice(0, 5));
            
        } catch (error) {
            console.error('Failed to load dashboard:', error);
        }
    }

    displayUpcomingShifts(shifts) {
        const container = document.getElementById('upcoming-shifts-list');
        
        if (shifts.length === 0) {
            container.innerHTML = '<p class="no-data">No upcoming shifts</p>';
            return;
        }

        const html = shifts.map(shift => `
            <div class="shift-item" style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; border-bottom: 1px solid var(--border-color);">
                <div>
                    <strong>${shift.template_name || 'Shift'}</strong><br>
                    <small>${this.formatDate(shift.shift_date)} ${shift.start_time} - ${shift.end_time}</small><br>
                    <small>${shift.activation_name}</small>
                </div>
                <div>
                    <span class="status-badge ${shift.confirmed_volunteers >= shift.min_volunteers ? 'status-confirmed' : 'status-pending'}">
                        ${shift.confirmed_volunteers}/${shift.min_volunteers} filled
                    </span>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    // Volunteer Methods
    async loadVolunteers() {
        try {
            const volunteers = await this.apiRequest('volunteers.php');
            this.volunteers = volunteers;
            this.displayVolunteers(volunteers);
        } catch (error) {
            console.error('Failed to load volunteers:', error);
        }
    }

    displayVolunteers(volunteers) {
        const tbody = document.getElementById('volunteers-tbody');
        
        if (volunteers.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="no-data">No volunteers found</td></tr>';
            return;
        }

        const html = volunteers.map(volunteer => `
            <tr>
                <td>${volunteer.first_name} ${volunteer.last_name}</td>
                <td>${volunteer.email}</td>
                <td>${volunteer.phone}</td>
                <td><span class="status-badge status-${volunteer.status}">${volunteer.status}</span></td>
                <td>
                    <div class="skills-tags">
                        ${(volunteer.skills || []).map(skill => 
                            `<span class="skill-tag">${skill.type}</span>`
                        ).join('')}
                    </div>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="app.editVolunteer(${volunteer.id})">Edit</button>
                    <button class="btn btn-sm btn-secondary" onclick="app.viewVolunteer(${volunteer.id})">View</button>
                </td>
            </tr>
        `).join('');

        tbody.innerHTML = html;
    }

    async showVolunteerForm(volunteerId = null) {
        const isEdit = volunteerId !== null;
        let volunteer = null;
        
        if (isEdit) {
            try {
                volunteer = await this.apiRequest(`volunteers.php/${volunteerId}`);
            } catch (error) {
                this.showToast('Failed to load volunteer details', 'error');
                return;
            }
        }

        const formHtml = `
            <div style="width: 600px; max-width: 90vw;">
                <h3 style="margin-bottom: 1.5rem; padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                    ${isEdit ? 'Edit' : 'Add'} Volunteer
                </h3>
                <form id="volunteer-form" style="padding: 1.5rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input type="text" id="first_name" required value="${volunteer?.first_name || ''}">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input type="text" id="last_name" required value="${volunteer?.last_name || ''}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" required value="${volunteer?.email || ''}">
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="phone">Phone *</label>
                            <input type="tel" id="phone" required value="${volunteer?.phone || ''}">
                        </div>
                        <div class="form-group">
                            <label for="alt_phone">Alt Phone</label>
                            <input type="tel" id="alt_phone" value="${volunteer?.alt_phone || ''}">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="preferred_contact_method">Preferred Contact Method</label>
                        <select id="preferred_contact_method">
                            <option value="email" ${volunteer?.preferred_contact_method === 'email' ? 'selected' : ''}>Email</option>
                            <option value="phone" ${volunteer?.preferred_contact_method === 'phone' ? 'selected' : ''}>Phone</option>
                            <option value="text" ${volunteer?.preferred_contact_method === 'text' ? 'selected' : ''}>Text</option>
                        </select>
                    </div>
                    
                    ${isEdit ? `
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status">
                                <option value="pending" ${volunteer?.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="active" ${volunteer?.status === 'active' ? 'selected' : ''}>Active</option>
                                <option value="inactive" ${volunteer?.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="training_completed" ${volunteer?.training_completed ? 'checked' : ''}>
                                Training Completed
                            </label>
                        </div>
                    </div>
                    ` : ''}
                    
                    <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
                        <button type="button" class="btn btn-secondary" onclick="app.closeModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">${isEdit ? 'Update' : 'Create'} Volunteer</button>
                    </div>
                </form>
            </div>
        `;

        this.showModal(formHtml);

        // Setup form submission
        document.getElementById('volunteer-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.saveVolunteer(volunteerId);
        });
    }

    async saveVolunteer(volunteerId = null) {
        const formData = {
            first_name: document.getElementById('first_name').value,
            last_name: document.getElementById('last_name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            alt_phone: document.getElementById('alt_phone').value,
            preferred_contact_method: document.getElementById('preferred_contact_method').value
        };

        // Add status and training for edits
        if (volunteerId) {
            formData.status = document.getElementById('status').value;
            formData.training_completed = document.getElementById('training_completed').checked;
        }

        try {
            if (volunteerId) {
                await this.apiRequest(`volunteers.php/${volunteerId}`, 'PUT', formData);
                this.showToast('Volunteer updated successfully', 'success');
            } else {
                await this.apiRequest('volunteers.php', 'POST', formData);
                this.showToast('Volunteer created successfully', 'success');
            }

            this.closeModal();
            if (this.currentSection === 'volunteers') {
                await this.loadVolunteers();
            }
        } catch (error) {
            this.showToast('Failed to save volunteer', 'error');
        }
    }

    editVolunteer(id) {
        this.showVolunteerForm(id);
    }

    async viewVolunteer(id) {
        try {
            const volunteer = await this.apiRequest(`volunteers.php/${id}`);
            // Implement volunteer detail view
            console.log('View volunteer:', volunteer);
        } catch (error) {
            this.showToast('Failed to load volunteer details', 'error');
        }
    }

    filterVolunteers() {
        const statusFilter = document.getElementById('status-filter').value;
        const skillFilter = document.getElementById('skill-filter').value;
        
        let filtered = this.volunteers;

        if (statusFilter) {
            filtered = filtered.filter(v => v.status === statusFilter);
        }

        if (skillFilter) {
            filtered = filtered.filter(v => 
                v.skills && v.skills.some(s => s.type === skillFilter)
            );
        }

        this.displayVolunteers(filtered);
    }

    // Scheduling Methods
    async loadScheduling() {
        try {
            // Set default date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('schedule-date').value = today;
            
            // Load activations for filter
            const activations = await this.apiRequest('activations.php');
            this.populateActivationFilter(activations);
            
            // Load today's schedule
            await this.loadSchedule();
        } catch (error) {
            console.error('Failed to load scheduling:', error);
            this.showToast('Failed to load scheduling data', 'error');
        }
    }

    populateActivationFilter(activations) {
        const select = document.getElementById('activation-filter');
        const options = activations.map(a => 
            `<option value="${a.id}">${a.name}</option>`
        ).join('');
        select.innerHTML = '<option value="">All Activations</option>' + options;
    }

    async loadSchedule() {
        const date = document.getElementById('schedule-date').value;
        const activationId = document.getElementById('activation-filter').value;
        
        try {
            let endpoint = 'shifts.php?';
            if (date) endpoint += `date=${date}&`;
            if (activationId) endpoint += `activation_id=${activationId}&`;
            
            const shifts = await this.apiRequest(endpoint.slice(0, -1));
            this.displaySchedule(shifts);
        } catch (error) {
            console.error('Failed to load schedule:', error);
        }
    }

    displaySchedule(shifts) {
        const container = document.getElementById('schedule-grid');
        
        if (shifts.length === 0) {
            container.innerHTML = '<p class="no-data">No shifts scheduled for this date</p>';
            return;
        }

        const html = `
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Shift</th>
                        <th>Time</th>
                        <th>Activation</th>
                        <th>Volunteers</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${shifts.map(shift => `
                        <tr>
                            <td>${shift.template_name || 'Custom Shift'}</td>
                            <td>${shift.start_time} - ${shift.end_time}</td>
                            <td>${shift.activation_name}</td>
                            <td>
                                <span class="status-badge ${shift.confirmed_volunteers >= shift.min_volunteers ? 'status-confirmed' : 'status-pending'}">
                                    ${shift.assigned_volunteers}/${shift.min_volunteers}
                                </span>
                            </td>
                            <td><span class="status-badge status-${shift.status}">${shift.status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="app.manageShift(${shift.id})">Manage</button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        
        container.innerHTML = html;
    }

    // Communication Methods
    async loadCommunications() {
        try {
            // Load recent communications
            const communications = await this.apiRequest('communications.php?recent=1');
            this.displayRecentCommunications(communications);
        } catch (error) {
            console.error('Failed to load communications:', error);
        }
    }

    displayRecentCommunications(communications) {
        const container = document.getElementById('recent-communications');
        
        if (communications.length === 0) {
            container.innerHTML = '<p class="no-data">No recent communications</p>';
            return;
        }

        const html = communications.map(comm => `
            <div style="padding: 0.75rem; border-bottom: 1px solid var(--border-color);">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div>
                        <strong>${comm.subject}</strong><br>
                        <small>${comm.type} • ${comm.method} • ${this.formatDateTime(comm.sent_at)}</small>
                    </div>
                    <span class="status-badge status-${comm.delivery_status}">${comm.delivery_status}</span>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    async sendMessage() {
        const formData = {
            type: document.getElementById('message-type').value,
            recipients: document.getElementById('recipients').value,
            subject: document.getElementById('message-subject').value,
            message: document.getElementById('message-body').value
        };

        try {
            await this.apiRequest('communications.php', 'POST', formData);
            this.showToast('Message sent successfully', 'success');
            document.getElementById('message-form').reset();
            await this.loadCommunications();
        } catch (error) {
            this.showToast('Failed to send message', 'error');
        }
    }

    // Reports Methods
    async loadReports() {
        try {
            // Load report data
            console.log('Loading reports...');
        } catch (error) {
            console.error('Failed to load reports:', error);
        }
    }

    // Utility Methods
    showModal(content) {
        document.getElementById('modal-body').innerHTML = content;
        document.getElementById('modal').classList.add('active');
    }

    closeModal() {
        document.getElementById('modal').classList.remove('active');
    }

    showLoading() {
        document.getElementById('loading').classList.remove('hidden');
    }

    hideLoading() {
        document.getElementById('loading').classList.add('hidden');
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        
        document.getElementById('toast-container').appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString();
    }

    formatDateTime(dateTimeString) {
        return new Date(dateTimeString).toLocaleString();
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.app = new VolunteerApp();
});

// Global functions for onclick handlers
function showVolunteerForm() {
    app.showVolunteerForm();
}

function filterVolunteers() {
    app.filterVolunteers();
}

function loadSchedule() {
    app.loadSchedule();
}

function closeModal() {
    app.closeModal();
}