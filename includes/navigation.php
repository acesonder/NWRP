<?php
/**
 * Navigation helper for NWRP systems
 * Provides consistent navigation across all applications
 */

function renderNavigation($currentSystem = '') {
    $portalUrl = '/';
    $coordinationUrl = '/coordination-system/';
    $proposalsUrl = '/proposals/webapp/';
    
    // Adjust URLs based on current location
    if (strpos($_SERVER['REQUEST_URI'], '/coordination-system/') !== false) {
        $portalUrl = '../';
        $proposalsUrl = '../proposals/webapp/';
    } elseif (strpos($_SERVER['REQUEST_URI'], '/proposals/webapp/') !== false) {
        $portalUrl = '../../';
        $coordinationUrl = '../../coordination-system/';
    }
    
    return '
    <div class="nwrp-nav-bar" style="background: #2563eb; color: white; padding: 0.75rem 1rem; font-family: Inter, sans-serif;">
        <div style="display: flex; justify-content: space-between; align-items: center; max-width: 1200px; margin: 0 auto;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 1.5rem;">🏠</span>
                <span style="font-weight: 600;">NWRP Portal</span>
            </div>
            <nav style="display: flex; gap: 1rem; align-items: center;">
                <a href="' . $portalUrl . '" style="color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px; ' . ($currentSystem === 'portal' ? 'background: rgba(255,255,255,0.2);' : '') . '">
                    🌟 Main Portal
                </a>
                <a href="' . $coordinationUrl . '" style="color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px; ' . ($currentSystem === 'coordination' ? 'background: rgba(255,255,255,0.2);' : '') . '">
                    👥 Coordination
                </a>
                <a href="' . $proposalsUrl . '" style="color: white; text-decoration: none; padding: 0.5rem 1rem; border-radius: 6px; ' . ($currentSystem === 'proposals' ? 'background: rgba(255,255,255,0.2);' : '') . '">
                    💡 Proposals
                </a>
            </nav>
        </div>
    </div>';
}

function getSystemBreadcrumb($currentSystem = '', $currentPage = '') {
    $systems = [
        'portal' => '🌟 NWRP Portal',
        'coordination' => '👥 Volunteer Coordination System', 
        'proposals' => '💡 Community Proposals System'
    ];
    
    $systemName = $systems[$currentSystem] ?? 'NWRP';
    
    $breadcrumb = '<div style="background: #f8fafc; padding: 0.5rem 1rem; border-bottom: 1px solid #e2e8f0; font-size: 0.875rem; color: #64748b;">';
    $breadcrumb .= '<span>' . $systemName . '</span>';
    
    if ($currentPage) {
        $breadcrumb .= ' <span style="margin: 0 0.5rem;">→</span> <span style="color: #1e293b;">' . $currentPage . '</span>';
    }
    
    $breadcrumb .= '</div>';
    
    return $breadcrumb;
}