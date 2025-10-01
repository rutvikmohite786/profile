<?php
/**
 * Topmate Clone - Homepage
 * Displays available experts and their profiles
 */
require_once 'includes/auth.php';
require_once 'config/database.php';


// Get all active users (experts)
$experts = fetchAll("SELECT * FROM users WHERE is_active = 1 AND is_admin = 0 ORDER BY created_at DESC");

// Include header
include 'includes/header.php';
?>

<div class="container">
    <!-- Hero Section -->
    <section class="hero hero-animated" style="background: linear-gradient(135deg, #6366f1, #a855f7, #ec4899); background-size: 200% 200%; animation: gradient 8s ease infinite; color: white; padding: 5rem 2rem; text-align: center; margin: 2rem 0 3rem; border-radius: var(--radius-xl); box-shadow: 0 20px 40px rgba(99, 102, 241, 0.3); position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); opacity: 0.5;"></div>
        <div class="container" style="position: relative; z-index: 1;">
            <h1 style="font-size: 3.5rem; margin-bottom: 1.5rem; font-weight: 800; letter-spacing: -0.02em; text-shadow: 0 2px 10px rgba(0,0,0,0.1);">Connect with Top Experts</h1>
            <p style="font-size: 1.35rem; margin-bottom: 2.5rem; opacity: 0.95; max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.7;">Book 1-on-1 sessions with industry professionals and accelerate your growth. Learn from the best and achieve your goals faster.</p>
            <?php if (!Auth::isLoggedIn()): ?>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="register.php" class="btn btn-gradient" style="padding: 1rem 2.5rem; font-size: 1rem; background: white; color: var(--primary-color); box-shadow: 0 4px 15px rgba(255,255,255,0.3); animation: none;">🚀 Get Started Free</a>
                    <a href="login.php" class="btn" style="padding: 1rem 2.5rem; font-size: 1rem; background: rgba(255,255,255,0.1); color: white; border: 2px solid rgba(255,255,255,0.5); backdrop-filter: blur(10px);">Sign In</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Featured Experts -->
    <section class="featured-experts" style="margin-bottom: 4rem;">
        <div style="text-align: center; margin-bottom: 3rem;">
            <div style="display: inline-block; padding: 0.5rem 1.5rem; background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(168,85,247,0.1)); border-radius: 50px; margin-bottom: 1rem;">
                <span style="font-size: 0.9rem; font-weight: 600; background: linear-gradient(135deg, var(--primary-color), var(--accent-purple)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">✨ FEATURED EXPERTS</span>
            </div>
            <h2 style="font-size: 2.8rem; margin-bottom: 1rem; color: var(--text-primary); font-weight: 800; letter-spacing: -0.02em;">Meet Our Expert Mentors</h2>
            <p style="font-size: 1.15rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">Connect with verified professionals who can help you achieve your goals and unlock your potential</p>
        </div>

        <?php if (empty($experts)): ?>
            <div class="card" style="text-align: center; padding: 3rem;">
                <h3 style="color: var(--text-secondary); margin-bottom: 1rem;">No Experts Available</h3>
                <p style="color: var(--text-light);">Check back later for expert profiles.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-3">
                <?php foreach ($experts as $expert): ?>
                    <div class="card expert-card">
                        <div style="padding: 2rem 1.5rem; text-align: center;">
                            <div class="expert-avatar" style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #a855f7, #ec4899); margin: 0 auto 1.25rem; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 700; box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3); border: 4px solid white;">
                                <?= strtoupper(substr($expert['first_name'], 0, 1) . substr($expert['last_name'], 0, 1)) ?>
                            </div>
                            <h3 style="font-size: 1.35rem; margin-bottom: 0.5rem; color: var(--text-primary); font-weight: 700;">
                                <?= htmlspecialchars($expert['first_name'] . ' ' . $expert['last_name']) ?>
                            </h3>
                            <p style="color: var(--text-light); font-size: 0.85rem; margin-bottom: 1.25rem;">
                                @<?= htmlspecialchars($expert['username']) ?>
                            </p>
                            
                            <?php if ($expert['bio']): ?>
                                <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 1.25rem; line-height: 1.6; min-height: 60px;">
                                    <?= htmlspecialchars(substr($expert['bio'], 0, 100)) ?><?= strlen($expert['bio']) > 100 ? '...' : '' ?>
                                </p>
                            <?php endif; ?>

                            <?php if ($expert['expertise']): ?>
                                <div style="margin-bottom: 1.5rem; display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center;">
                                    <?php 
                                    $skills = explode(',', $expert['expertise']);
                                    $skills = array_slice($skills, 0, 3);
                                    $colors = ['rgba(99, 102, 241, 0.1)', 'rgba(168, 85, 247, 0.1)', 'rgba(236, 72, 153, 0.1)'];
                                    $textColors = ['#6366f1', '#a855f7', '#ec4899'];
                                    $index = 0;
                                    foreach ($skills as $skill): 
                                        $skill = trim($skill);
                                        if ($skill):
                                    ?>
                                        <span class="skill-tag-animated" style="display: inline-block; background: <?= $colors[$index % 3] ?>; color: <?= $textColors[$index % 3] ?>; padding: 0.4rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: 600; border: 1px solid <?= $colors[$index % 3] ?>;"><?= htmlspecialchars($skill) ?></span>
                                    <?php 
                                        $index++;
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            <?php endif; ?>

                            <div style="background: linear-gradient(135deg, rgba(99,102,241,0.05), rgba(168,85,247,0.05)); padding: 1rem; border-radius: var(--radius-lg); margin-bottom: 1.5rem;">
                                <strong style="color: var(--primary-color); font-size: 1.75rem; font-weight: 800;">$<?= number_format($expert['price_per_session'], 2) ?></strong>
                                <span style="color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">/session</span>
                            </div>

                            <a href="profile.php?id=<?= $expert['id'] ?>" class="btn btn-primary" style="width: 100%; padding: 0.875rem; font-weight: 600; position: relative; z-index: 1;">👁️ View Profile</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- Features Section -->
    <section class="features" style="margin-top: 5rem; padding: 4rem 2rem; background: linear-gradient(135deg, rgba(99,102,241,0.03), rgba(168,85,247,0.03)); border-radius: var(--radius-xl); position: relative; overflow: hidden;">
        <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(168,85,247,0.1)); border-radius: 50%; filter: blur(40px);"></div>
        <div style="position: absolute; bottom: -50px; left: -50px; width: 200px; height: 200px; background: linear-gradient(135deg, rgba(236,72,153,0.1), rgba(168,85,247,0.1)); border-radius: 50%; filter: blur(40px);"></div>
        
        <div class="container" style="position: relative; z-index: 1;">
            <div style="text-align: center; margin-bottom: 4rem;">
                <h2 style="font-size: 2.8rem; margin-bottom: 1rem; color: var(--text-primary); font-weight: 800; letter-spacing: -0.02em;">Why Choose Topmate?</h2>
                <p style="font-size: 1.15rem; color: var(--text-secondary); max-width: 600px; margin: 0 auto;">Everything you need to connect with the right mentors and accelerate your success</p>
            </div>

            <div class="grid grid-3">
                <div class="feature-card" style="text-align: center; padding: 2.5rem; background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-md);">
                    <div class="feature-icon" style="width: 80px; height: 80px; background: linear-gradient(135deg, #6366f1, #818cf8); border-radius: 20px; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);">🎯</div>
                    <h3 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1.4rem; font-weight: 700;">Expert Matching</h3>
                    <p style="color: var(--text-secondary); line-height: 1.7;">Find mentors who match your specific needs and goals with our intelligent matching system</p>
                </div>

                <div class="feature-card" style="text-align: center; padding: 2.5rem; background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-md);">
                    <div class="feature-icon" style="width: 80px; height: 80px; background: linear-gradient(135deg, #10b981, #34d399); border-radius: 20px; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">📅</div>
                    <h3 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1.4rem; font-weight: 700;">Easy Booking</h3>
                    <p style="color: var(--text-secondary); line-height: 1.7;">Schedule sessions at your convenience with our simple and intuitive booking system</p>
                </div>

                <div class="feature-card" style="text-align: center; padding: 2.5rem; background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-md);">
                    <div class="feature-icon" style="width: 80px; height: 80px; background: linear-gradient(135deg, #ec4899, #f472b6); border-radius: 20px; margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; font-size: 2rem; box-shadow: 0 10px 25px rgba(236, 72, 153, 0.3);">💬</div>
                    <h3 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1.4rem; font-weight: 700;">Direct Communication</h3>
                    <p style="color: var(--text-secondary); line-height: 1.7;">Get personalized guidance through one-on-one sessions with industry experts</p>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
/* Page-specific animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Stagger animation for cards */
.expert-card {
    opacity: 0;
    animation: fadeInUp 0.6s ease-out forwards;
}

.expert-card:nth-child(1) { animation-delay: 0.1s; }
.expert-card:nth-child(2) { animation-delay: 0.2s; }
.expert-card:nth-child(3) { animation-delay: 0.3s; }
.expert-card:nth-child(4) { animation-delay: 0.4s; }
.expert-card:nth-child(5) { animation-delay: 0.5s; }
.expert-card:nth-child(6) { animation-delay: 0.6s; }
</style>

<?php include 'includes/footer.php'; ?>

