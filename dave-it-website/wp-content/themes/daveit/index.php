<?php
/**
 * Dave IT — Homepage Template
 * Handles the main front-page display.
 */

get_header();
?>

<!-- =========================================
     HERO
     ========================================= -->
<section id="hero">
    <div class="hero-bg"></div>
    <div class="hero-grid"></div>
    <div class="container">
        <div class="hero-content">
            <div class="badge">⚡ Trusted by 500+ Businesses</div>
            <h1 class="hero-title">
                IT Solutions<br>
                Built for<br>
                <span class="line-accent">Business Growth</span>
            </h1>
            <p class="hero-subtitle">
                We keep your technology running so you can focus on what matters — growing your business. 24/7 support, proactive monitoring, zero downtime.
            </p>
            <div class="hero-actions">
                <a href="#contact" class="btn btn-primary btn-lg">Get a Free Consultation →</a>
                <a href="#services" class="btn btn-outline btn-lg">View Our Services</a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <strong>500+</strong>
                    <span>Businesses Served</span>
                </div>
                <div class="stat-item">
                    <strong>99.9%</strong>
                    <span>Uptime SLA</span>
                </div>
                <div class="stat-item">
                    <strong>15 min</strong>
                    <span>Avg Response Time</span>
                </div>
                <div class="stat-item">
                    <strong>15+</strong>
                    <span>Years Experience</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================
     TRUST BAR
     ========================================= -->
<div id="trust-bar">
    <div class="container">
        <div class="trust-inner">
            <span class="trust-label">Trusted by</span>
            <div class="trust-divider"></div>
            <div class="trust-logos">
                <span class="trust-logo">Microsoft</span>
                <span class="trust-logo">Cisco</span>
                <span class="trust-logo">VMware</span>
                <span class="trust-logo">Fortinet</span>
                <span class="trust-logo">Veeam</span>
                <span class="trust-logo">Datto</span>
                <span class="trust-logo">SentinelOne</span>
            </div>
        </div>
    </div>
</div>

<!-- =========================================
     SERVICES
     ========================================= -->
<section id="services" class="section-padding">
    <div class="container">
        <div class="text-center" data-reveal>
            <div class="badge">What We Do</div>
            <h2 class="section-title">Complete IT Services for <span class="highlight">Modern Business</span></h2>
            <p class="section-subtitle">From day-to-day helpdesk support to enterprise cloud infrastructure, we've got every aspect of your IT covered.</p>
        </div>

        <div class="services-grid">
            <?php
            $services = [
                [ '🖥️', 'Managed IT Support',     'Round-the-clock helpdesk and proactive monitoring. We fix issues before you even notice them — guaranteed response within 15 minutes.', 'Learn more' ],
                [ '🔐', 'Cybersecurity',           'Multi-layered security with next-gen endpoint protection, SIEM, phishing simulation, and compliance frameworks (ISO 27001, SOC 2).', 'Learn more' ],
                [ '☁️', 'Cloud Solutions',          'Seamless migration and management of Azure, AWS, and hybrid cloud environments. Scale up or down as your business demands.', 'Learn more' ],
                [ '📧', 'Microsoft 365',            'Full deployment, migration, and ongoing management of M365, Teams, Exchange, SharePoint, and Intune device management.', 'Learn more' ],
                [ '🌐', 'Network Infrastructure',  'Design, installation, and ongoing management of enterprise-grade wired and wireless network infrastructure for any size office.', 'Learn more' ],
                [ '💡', 'IT Consulting',            'Strategic technology roadmapping and virtual CTO services to align your IT investments with your business goals.', 'Learn more' ],
            ];

            foreach ( $services as $i => [ $icon, $title, $desc, $cta ] ) {
                $delay = $i * 80;
                echo '<div class="service-card" data-reveal style="transition-delay:' . $delay . 'ms">';
                echo '  <div class="service-icon">' . $icon . '</div>';
                echo '  <h3>' . esc_html( $title ) . '</h3>';
                echo '  <p>' . esc_html( $desc ) . '</p>';
                echo '  <a href="#contact" class="service-link">' . esc_html( $cta ) . ' →</a>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- =========================================
     WHY US
     ========================================= -->
<section id="why-us" class="section-padding">
    <div class="container">
        <div class="why-grid">
            <div data-reveal>
                <div class="badge">Why Dave IT</div>
                <h2 class="section-title">Technology That <span class="highlight">Works for You</span></h2>
                <p class="section-subtitle" style="margin-bottom:40px;">We're not just a vendor — we're your strategic technology partner, invested in your success.</p>

                <div class="why-features">
                    <?php
                    $features = [
                        [ '⚡', '15-Minute Response Guarantee',    'Our SLA is iron-clad. Critical issues are responded to within 15 minutes, any time of day or night.' ],
                        [ '🛡️', 'Proactive, Not Reactive',         'Our monitoring tools detect and resolve 90% of issues before they impact your users.' ],
                        [ '📈', 'Business-Aligned IT Strategy',    'Quarterly technology reviews to ensure your IT roadmap supports your growth objectives.' ],
                        [ '🤝', 'Dedicated Account Manager',       'A single point of contact who knows your business, your team, and your technology environment.' ],
                    ];
                    foreach ( $features as [ $icon, $title, $desc ] ) {
                        echo '<div class="why-feature">';
                        echo '  <div class="feature-icon">' . $icon . '</div>';
                        echo '  <div class="feature-text"><h4>' . esc_html( $title ) . '</h4><p>' . esc_html( $desc ) . '</p></div>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

            <div data-reveal style="transition-delay:200ms">
                <div class="why-card">
                    <div class="why-card-title">System Uptime — Last 12 Months</div>
                    <div class="uptime-ring">
                        <svg viewBox="0 0 160 160" width="160" height="160">
                            <defs>
                                <linearGradient id="ringGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#0057FF"/>
                                    <stop offset="100%" style="stop-color:#00C2FF"/>
                                </linearGradient>
                            </defs>
                            <circle class="bg"       cx="80" cy="80" r="70"/>
                            <circle class="progress" cx="80" cy="80" r="70"/>
                        </svg>
                        <div class="uptime-label">
                            <strong>99.9%</strong>
                            <span>Uptime</span>
                        </div>
                    </div>

                    <div class="metrics-row">
                        <div class="metric"><strong>15 min</strong><span>Avg Response</span></div>
                        <div class="metric"><strong>4.9★</strong><span>Client Rating</span></div>
                        <div class="metric"><strong>500+</strong><span>Clients</span></div>
                        <div class="metric"><strong>24/7</strong><span>Monitoring</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================
     PROCESS
     ========================================= -->
<section id="process" class="section-padding">
    <div class="container">
        <div class="text-center" data-reveal>
            <div class="badge">How It Works</div>
            <h2 class="section-title">Up and Running in <span class="highlight">4 Simple Steps</span></h2>
            <p class="section-subtitle">Onboarding with Dave IT is fast and painless. Most clients are fully managed within 2 weeks.</p>
        </div>

        <div class="process-steps">
            <?php
            $steps = [
                [ '1', 'Free Consultation',     'We learn about your business, your current IT environment, and your goals. No obligation, no pushy sales.' ],
                [ '2', 'Custom IT Assessment',  'Our engineers conduct a thorough audit of your infrastructure, security posture, and identify quick wins.' ],
                [ '3', 'Tailored Proposal',     'You receive a transparent, flat-rate proposal with no hidden fees — designed specifically for your needs.' ],
                [ '4', 'Seamless Onboarding',   'Our team handles the transition. We install monitoring tools, document your environment, and you\'re covered.' ],
            ];
            foreach ( $steps as $i => [ $num, $title, $desc ] ) {
                $delay = $i * 100;
                echo '<div class="process-step" data-reveal style="transition-delay:' . $delay . 'ms">';
                echo '  <div class="step-number">' . $num . '</div>';
                echo '  <h3>' . esc_html( $title ) . '</h3>';
                echo '  <p>' . esc_html( $desc ) . '</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- =========================================
     TESTIMONIALS
     ========================================= -->
<section id="testimonials" class="section-padding">
    <div class="container">
        <div class="text-center" data-reveal>
            <div class="badge">Client Stories</div>
            <h2 class="section-title">Trusted by <span class="highlight">Hundreds of Businesses</span></h2>
            <p class="section-subtitle">Don't just take our word for it — here's what our clients have to say.</p>
        </div>

        <div class="testimonials-grid">
            <?php
            $testimonials = [
                [
                    'quote'   => 'Dave IT transformed our entire IT infrastructure. We went from weekly outages to 99.9% uptime. Their team is incredibly responsive and proactive. I honestly don\'t know how we managed without them.',
                    'name'    => 'Sarah Mitchell',
                    'role'    => 'CEO, Apex Legal Group',
                    'initials'=> 'SM',
                    'color'   => '#7C3AED',
                ],
                [
                    'quote'   => 'The cybersecurity audit they ran revealed 12 critical vulnerabilities we didn\'t know about. Within a week everything was patched and we had a full security framework in place. Exceptional work.',
                    'name'    => 'James Thornton',
                    'role'    => 'IT Director, NorthStar Finance',
                    'initials'=> 'JT',
                    'color'   => '#0057FF',
                ],
                [
                    'quote'   => 'Our Microsoft 365 migration was seamless — zero downtime, zero data loss. The Dave IT team handled everything and kept us informed every step of the way. Highly recommend.',
                    'name'    => 'Linda Park',
                    'role'    => 'Operations Manager, Elevate Marketing',
                    'initials'=> 'LP',
                    'color'   => '#059669',
                ],
            ];
            foreach ( $testimonials as $t ) {
                echo '<div class="testimonial-card" data-reveal>';
                echo '  <div class="stars">★★★★★</div>';
                echo '  <blockquote>"' . esc_html( $t['quote'] ) . '"</blockquote>';
                echo '  <div class="testimonial-author">';
                echo '    <div class="author-avatar" style="background:' . $t['color'] . '">' . esc_html( $t['initials'] ) . '</div>';
                echo '    <div class="author-info"><strong>' . esc_html( $t['name'] ) . '</strong><span>' . esc_html( $t['role'] ) . '</span></div>';
                echo '  </div>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- =========================================
     PRICING
     ========================================= -->
<section id="pricing" class="section-padding">
    <div class="container">
        <div class="text-center" data-reveal>
            <div class="badge">Transparent Pricing</div>
            <h2 class="section-title">Simple, Flat-Rate <span class="highlight">IT Plans</span></h2>
            <p class="section-subtitle">No hidden fees. No surprise invoices. Just predictable monthly pricing that scales with your business.</p>
        </div>

        <div class="pricing-grid">
            <?php
            $plans = [
                [
                    'name'     => 'Essentials',
                    'price'    => '49',
                    'period'   => 'per user / month',
                    'popular'  => false,
                    'features' => [
                        'Helpdesk support (business hours)',
                        'Endpoint monitoring & patching',
                        'Antivirus & email security',
                        'Microsoft 365 management',
                        'Monthly IT health report',
                        '4-hour response SLA',
                    ],
                ],
                [
                    'name'     => 'Professional',
                    'price'    => '89',
                    'period'   => 'per user / month',
                    'popular'  => true,
                    'features' => [
                        'Everything in Essentials',
                        '24/7 helpdesk support',
                        '15-minute response SLA',
                        'Advanced cybersecurity (EDR)',
                        'Cloud backup & disaster recovery',
                        'Dedicated account manager',
                        'Quarterly strategy review',
                    ],
                ],
                [
                    'name'     => 'Enterprise',
                    'price'    => 'Custom',
                    'period'   => 'tailored to your needs',
                    'popular'  => false,
                    'features' => [
                        'Everything in Professional',
                        'On-site engineering support',
                        'Virtual CIO / CISO services',
                        'Custom SLA & SLO agreements',
                        'Multi-site management',
                        'Compliance management (SOC 2, ISO)',
                        'Priority escalation path',
                    ],
                ],
            ];
            foreach ( $plans as $plan ) {
                $popular_class = $plan['popular'] ? ' popular' : '';
                echo '<div class="pricing-card' . $popular_class . '" data-reveal>';
                if ( $plan['popular'] ) echo '<div class="popular-badge">Most Popular</div>';
                echo '  <div class="plan-name">' . esc_html( $plan['name'] ) . '</div>';
                echo '  <div class="plan-price">';
                if ( $plan['price'] === 'Custom' ) {
                    echo '<span class="amount" style="font-size:2.2rem;">Custom</span>';
                } else {
                    echo '<span class="currency">$</span><span class="amount">' . esc_html( $plan['price'] ) . '</span>';
                }
                echo '  </div>';
                echo '  <div class="plan-period">' . esc_html( $plan['period'] ) . '</div>';
                echo '  <ul class="plan-features">';
                foreach ( $plan['features'] as $f ) {
                    echo '    <li>' . esc_html( $f ) . '</li>';
                }
                echo '  </ul>';
                $btn_class = $plan['popular'] ? 'btn-primary' : 'btn-outline';
                echo '  <a href="#contact" class="btn ' . $btn_class . '" style="width:100%;justify-content:center;">Get Started</a>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- =========================================
     CONTACT
     ========================================= -->
<section id="contact" class="section-padding" style="background:var(--dark-2)">
    <div class="container">
        <div class="text-center" data-reveal>
            <div class="badge">Contact Us</div>
            <h2 class="section-title">Let's Talk About Your <span class="highlight">IT Needs</span></h2>
            <p class="section-subtitle">Fill in the form and one of our experts will reach out within one business day.</p>
        </div>

        <div style="max-width:640px;margin:0 auto;" data-reveal>
            <form id="contact-form" novalidate>
                <?php wp_nonce_field( 'daveit_contact_nonce', 'nonce' ); ?>
                <input type="hidden" name="action" value="daveit_contact">

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;">
                    <div>
                        <label for="cf-name" style="display:block;font-size:0.85rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;">Full Name *</label>
                        <input id="cf-name" name="name" type="text" required placeholder="Jane Smith"
                               style="width:100%;background:var(--dark-3);border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;color:var(--text);font-size:0.95rem;outline:none;transition:border-color .2s;"
                               onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                    </div>
                    <div>
                        <label for="cf-email" style="display:block;font-size:0.85rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;">Work Email *</label>
                        <input id="cf-email" name="email" type="email" required placeholder="jane@company.com"
                               style="width:100%;background:var(--dark-3);border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;color:var(--text);font-size:0.95rem;outline:none;transition:border-color .2s;"
                               onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                    </div>
                </div>

                <div style="margin-bottom:20px;">
                    <label for="cf-company" style="display:block;font-size:0.85rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;">Company</label>
                    <input id="cf-company" name="company" type="text" placeholder="Acme Corp"
                           style="width:100%;background:var(--dark-3);border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;color:var(--text);font-size:0.95rem;outline:none;transition:border-color .2s;"
                           onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'">
                </div>

                <div style="margin-bottom:20px;">
                    <label for="cf-service" style="display:block;font-size:0.85rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;">Service Interested In</label>
                    <select id="cf-service" name="service"
                            style="width:100%;background:var(--dark-3);border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;color:var(--text);font-size:0.95rem;outline:none;cursor:pointer;">
                        <option value="">Select a service...</option>
                        <option>Managed IT Support</option>
                        <option>Cybersecurity</option>
                        <option>Cloud Solutions</option>
                        <option>Microsoft 365</option>
                        <option>Network Infrastructure</option>
                        <option>IT Consulting</option>
                        <option>Not sure yet</option>
                    </select>
                </div>

                <div style="margin-bottom:28px;">
                    <label for="cf-message" style="display:block;font-size:0.85rem;font-weight:600;color:var(--text-muted);margin-bottom:8px;">Message *</label>
                    <textarea id="cf-message" name="message" required rows="5" placeholder="Tell us about your business and IT challenges..."
                              style="width:100%;background:var(--dark-3);border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;color:var(--text);font-size:0.95rem;outline:none;resize:vertical;transition:border-color .2s;"
                              onfocus="this.style.borderColor='var(--primary)'" onblur="this.style.borderColor='var(--border)'"></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;" id="cf-submit">
                    Send Message →
                </button>
                <div id="cf-response" style="margin-top:16px;text-align:center;font-size:0.92rem;"></div>
            </form>
        </div>
    </div>
</section>

<!-- =========================================
     CTA BANNER
     ========================================= -->
<section id="cta-section">
    <div class="container">
        <div class="cta-box" data-reveal>
            <h2>Ready to Stop Worrying About IT?</h2>
            <p>Join 500+ businesses that trust Dave IT to keep their technology running at peak performance.</p>
            <div class="cta-actions">
                <a href="#contact" class="btn btn-white btn-lg">Get a Free Consultation</a>
                <a href="tel:+18003283483" class="btn btn-ghost btn-lg">📞 Call Us Now</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
