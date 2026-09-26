<!-- ================= GEARGO PORTFOLIO & SECURITY DISCLAIMER MODAL ================= -->
<div id="geargo-disclaimer-overlay" class="geargo-disclaimer-overlay" style="display: none;" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="geargo-disclaimer-title">
    <div class="geargo-disclaimer-card">
        
        <!-- Header Badge & Title -->
        <div class="geargo-disclaimer-header">
            <div class="geargo-disclaimer-icon-wrapper">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <div>
                <span class="geargo-disclaimer-badge">Demonstration Notice</span>
                <h3 id="geargo-disclaimer-title" class="geargo-disclaimer-title">Portfolio Project & Advisory</h3>
            </div>
        </div>

        <!-- Body / Content -->
        <div class="geargo-disclaimer-body">
            <p class="geargo-disclaimer-lead">
                Welcome to <strong>GearGo</strong>. Please review this advisory before exploring the platform:
            </p>

            <ul class="geargo-disclaimer-list">
                <li>
                    <span class="geargo-disclaimer-list-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b71dc" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                            <line x1="8" y1="21" x2="16" y2="21"/>
                            <line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                    </span>
                    <div>
                        <strong>Portfolio & Demonstration Only:</strong>
                        This website is an educational, non-commercial portfolio project built to demonstrate full-stack e-commerce architecture.
                    </div>
                </li>

                <li>
                    <span class="geargo-disclaimer-list-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                    </span>
                    <div>
                        <strong>No Real Orders or Payments:</strong>
                        This system does <em>not</em> process real financial transactions, payment card charges, or product deliveries. All catalog items, checkouts, and orders are simulated.
                    </div>
                </li>

                <li>
                    <span class="geargo-disclaimer-list-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                            <line x1="12" y1="9" x2="12" y2="13"/>
                            <line x1="12" y1="17" x2="12.01" y2="17"/>
                        </svg>
                    </span>
                    <div>
                        <strong>Do NOT Provide Real Sensitive Data:</strong>
                        For your privacy and security, do <strong>not</strong> enter real passwords, actual credit/debit card numbers, or sensitive personal information during registration or checkout. Please use mock/dummy data only.
                    </div>
                </li>

                <li>
                    <span class="geargo-disclaimer-list-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                    </span>
                    <div>
                        <strong>Trademarks & Imagery:</strong>
                        All brand names, trademarks, and imagery belong to their respective owners and are displayed strictly under fair use for showcase purposes.
                    </div>
                </li>
            </ul>
        </div>

        <!-- Footer / Action Button -->
        <div class="geargo-disclaimer-footer">
            <button id="geargo-disclaimer-accept-btn" class="geargo-disclaimer-btn" type="button">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                I Understand & Acknowledge
            </button>
            <p class="geargo-disclaimer-subtext">This confirmation is saved locally and will not appear again on this browser.</p>
        </div>

    </div>
</div>

<style>
/* === GearGo Portfolio Disclaimer Modal Styles === */
.geargo-disclaimer-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(15, 23, 42, 0.72);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease, visibility 0.3s ease;
    box-sizing: border-box;
}

.geargo-disclaimer-overlay.active {
    opacity: 1;
    visibility: visible;
}

.geargo-disclaimer-card {
    background: #ffffff;
    max-width: 540px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    border-radius: 14px;
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.22);
    padding: 28px 26px 22px 26px;
    font-family: "Poppins", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    color: #1e293b;
    box-sizing: border-box;
    transform: translateY(20px) scale(0.97);
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.geargo-disclaimer-overlay.active .geargo-disclaimer-card {
    transform: translateY(0) scale(1);
}

.geargo-disclaimer-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
    padding-bottom: 14px;
    border-bottom: 1px solid #f1f5f9;
}

.geargo-disclaimer-icon-wrapper {
    width: 48px;
    height: 48px;
    min-width: 48px;
    border-radius: 12px;
    background-color: #eff6ff;
    color: #3b71dc;
    display: flex;
    align-items: center;
    justify-content: center;
}

.geargo-disclaimer-badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #3b71dc;
    background-color: #eff6ff;
    padding: 3px 8px;
    border-radius: 4px;
    margin-bottom: 4px;
}

.geargo-disclaimer-title {
    font-size: 19px;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
    line-height: 1.3;
}

.geargo-disclaimer-lead {
    font-size: 13.5px;
    color: #475569;
    line-height: 1.5;
    margin: 0 0 14px 0;
}

.geargo-disclaimer-list {
    list-style: none;
    padding: 0;
    margin: 0 0 18px 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.geargo-disclaimer-list li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 13px;
    line-height: 1.5;
    color: #334155;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 12px;
}

.geargo-disclaimer-list-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 2px;
    flex-shrink: 0;
}

.geargo-disclaimer-list strong {
    color: #0f172a;
    font-weight: 600;
    display: block;
    margin-bottom: 2px;
}

.geargo-disclaimer-footer {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    padding-top: 14px;
    border-top: 1px solid #f1f5f9;
}

.geargo-disclaimer-btn {
    width: 100%;
    background-color: #3b71dc;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 12px 20px;
    font-size: 14.5px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background-color 0.25s ease, transform 0.15s ease, box-shadow 0.2s ease;
    box-shadow: 0 4px 12px rgba(59, 113, 220, 0.28);
}

.geargo-disclaimer-btn:hover {
    background-color: rgb(65, 105, 255);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(59, 113, 220, 0.35);
}

.geargo-disclaimer-btn:active {
    transform: translateY(0);
}

.geargo-disclaimer-subtext {
    font-size: 11px;
    color: #94a3b8;
    margin: 0;
    text-align: center;
}

/* Mobile responsive adjustments */
@media (max-width: 580px) {
    .geargo-disclaimer-card {
        padding: 20px 16px 18px 16px;
    }
    .geargo-disclaimer-title {
        font-size: 17px;
    }
    .geargo-disclaimer-list li {
        font-size: 12px;
        padding: 8px 10px;
    }
    .geargo-disclaimer-btn {
        font-size: 13.5px;
        padding: 11px 16px;
    }
}
</style>

<script>
(function() {
    const STORAGE_KEY = 'geargo_portfolio_disclaimer_accepted';
    const overlay = document.getElementById('geargo-disclaimer-overlay');
    const acceptBtn = document.getElementById('geargo-disclaimer-accept-btn');

    if (!overlay || !acceptBtn) return;

    // Check if previously acknowledged
    function checkDisclaimer() {
        try {
            const hasAccepted = localStorage.getItem(STORAGE_KEY);
            if (!hasAccepted) {
                // First-time visit: show modal
                overlay.style.display = 'flex';
                // Trigger reflow for CSS transition
                overlay.offsetHeight;
                overlay.classList.add('active');
                overlay.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }
        } catch (e) {
            // Fallback if localStorage is disabled/restricted
            console.warn('GearGo Disclaimer: localStorage not accessible.', e);
        }
    }

    // Acknowledge handler
    function acceptDisclaimer() {
        try {
            localStorage.setItem(STORAGE_KEY, 'true');
        } catch (e) {
            console.warn('GearGo Disclaimer: Unable to write to localStorage.', e);
        }
        overlay.classList.remove('active');
        overlay.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = ''; // Restore scrolling
        setTimeout(() => {
            overlay.style.display = 'none';
        }, 300);
    }

    acceptBtn.addEventListener('click', acceptDisclaimer);

    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkDisclaimer);
    } else {
        checkDisclaimer();
    }
})();
</script>
<!-- ================= END DISCLAIMER MODAL ================= -->
