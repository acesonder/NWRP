/**
 * Warming Room Proposal System - Main JavaScript
 */

// Utility function for AJAX requests
function ajax(url, method = 'GET', data = null, callback = null) {
    const xhr = new XMLHttpRequest();
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (callback) callback(null, response);
                } catch (e) {
                    if (callback) callback(e, null);
                }
            } else {
                if (callback) callback(new Error('Request failed: ' + xhr.status), null);
            }
        }
    };
    
    xhr.open(method, url, true);
    
    if (method === 'POST' && data) {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        const params = new URLSearchParams(data).toString();
        xhr.send(params);
    } else {
        xhr.send();
    }
}

// Show loading spinner
function showLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '<div class="spinner"></div>';
    }
}

// Show alert message
function showAlert(message, type = 'info', elementId = 'alert-container') {
    const container = document.getElementById(elementId);
    if (!container) return;
    
    const alertClass = 'alert-' + type;
    const alert = document.createElement('div');
    alert.className = 'alert ' + alertClass;
    alert.textContent = message;
    
    container.appendChild(alert);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}

// Form validation helper
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = 'var(--danger-color)';
            isValid = false;
        } else {
            field.style.borderColor = 'var(--border-color)';
        }
    });
    
    return isValid;
}

// Load proposals dynamically
function loadProposals(status = '', containerId = 'proposals-list') {
    showLoading(containerId);
    
    const url = 'api/proposals.php?action=list' + (status ? '&status=' + status : '');
    
    ajax(url, 'GET', null, function(error, response) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        if (error || !response.success) {
            container.innerHTML = '<div class="alert alert-error">Failed to load proposals</div>';
            return;
        }
        
        if (response.data.length === 0) {
            container.innerHTML = '<p class="text-center">No proposals found.</p>';
            return;
        }
        
        let html = '<table class="table"><thead><tr>' +
                   '<th>Title</th><th>Author</th><th>Status</th><th>Created</th><th>Actions</th>' +
                   '</tr></thead><tbody>';
        
        response.data.forEach(proposal => {
            const statusClass = 'status-' + proposal.status.toLowerCase().replace(' ', '-');
            html += '<tr>' +
                    '<td><a href="view_proposal.php?id=' + proposal.id + '">' + escapeHtml(proposal.title) + '</a></td>' +
                    '<td>' + escapeHtml(proposal.author) + '</td>' +
                    '<td><span class="status-badge ' + statusClass + '">' + proposal.status + '</span></td>' +
                    '<td>' + formatDate(proposal.created_at) + '</td>' +
                    '<td><a href="view_proposal.php?id=' + proposal.id + '" class="btn btn-primary">View</a></td>' +
                    '</tr>';
        });
        
        html += '</tbody></table>';
        container.innerHTML = html;
    });
}

// Submit proposal form
function submitProposal(formId) {
    if (!validateForm(formId)) {
        showAlert('Please fill in all required fields', 'error');
        return false;
    }
    
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    data.action = 'create';
    
    ajax('api/proposals.php', 'POST', data, function(error, response) {
        if (error || !response.success) {
            showAlert(response?.message || 'Failed to submit proposal', 'error');
        } else {
            showAlert('Proposal submitted successfully!', 'success');
            form.reset();
            setTimeout(() => {
                window.location.href = 'view_proposal.php?id=' + response.data.id;
            }, 1500);
        }
    });
    
    return false;
}

// Submit comment
function submitComment(proposalId) {
    const commentText = document.getElementById('comment-text');
    const author = document.getElementById('comment-author');
    
    if (!commentText.value.trim() || !author.value.trim()) {
        showAlert('Please fill in all fields', 'error');
        return;
    }
    
    const data = {
        action: 'add_comment',
        proposal_id: proposalId,
        author: author.value,
        comment_text: commentText.value
    };
    
    ajax('api/proposals.php', 'POST', data, function(error, response) {
        if (error || !response.success) {
            showAlert('Failed to submit comment', 'error');
        } else {
            showAlert('Comment added successfully!', 'success');
            commentText.value = '';
            author.value = '';
            loadComments(proposalId);
        }
    });
}

// Load comments for a proposal
function loadComments(proposalId) {
    ajax('api/proposals.php?action=comments&proposal_id=' + proposalId, 'GET', null, function(error, response) {
        const container = document.getElementById('comments-list');
        if (!container) return;
        
        if (error || !response.success) {
            container.innerHTML = '<div class="alert alert-error">Failed to load comments</div>';
            return;
        }
        
        if (response.data.length === 0) {
            container.innerHTML = '<p>No comments yet. Be the first to comment!</p>';
            return;
        }
        
        let html = '';
        response.data.forEach(comment => {
            html += '<div class="card">' +
                    '<strong>' + escapeHtml(comment.author) + '</strong> ' +
                    '<small>' + formatDate(comment.created_at) + '</small>' +
                    '<p>' + escapeHtml(comment.comment_text) + '</p>' +
                    '</div>';
        });
        
        container.innerHTML = html;
    });
}

// Test database connection
function testDatabaseConnection() {
    showLoading('test-result');
    
    ajax('api/test_connection.php', 'GET', null, function(error, response) {
        const container = document.getElementById('test-result');
        if (!container) return;
        
        if (error) {
            container.innerHTML = '<div class="alert alert-error">Connection test failed: ' + error.message + '</div>';
        } else if (response.success) {
            container.innerHTML = '<div class="alert alert-success">' + response.message + '</div>';
        } else {
            container.innerHTML = '<div class="alert alert-error">' + response.message + '</div>';
        }
    });
}

// Get system configuration
function loadConfig() {
    ajax('api/config.php?action=get', 'GET', null, function(error, response) {
        if (error || !response.success) return;
        
        // Populate config form fields
        Object.keys(response.data).forEach(key => {
            const field = document.getElementById('config-' + key);
            if (field) {
                field.value = response.data[key];
            }
        });
    });
}

// Save system configuration
function saveConfig() {
    const form = document.getElementById('config-form');
    if (!form) return;
    
    const formData = new FormData(form);
    const data = { action: 'update' };
    
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    
    ajax('api/config.php', 'POST', data, function(error, response) {
        if (error || !response.success) {
            showAlert('Failed to save configuration', 'error');
        } else {
            showAlert('Configuration saved successfully!', 'success');
        }
    });
}

// Utility: Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Utility: Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-load proposals if container exists
    if (document.getElementById('proposals-list')) {
        loadProposals();
    }
    
    // Auto-load config if form exists
    if (document.getElementById('config-form')) {
        loadConfig();
    }
});
