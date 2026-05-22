<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Story — Sauni</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/pages/our-story.css?v=<?= time() ?>">
</head>
<body>

    <nav class="nav">
        <a href="landing.php" class="logo">S<span>🔥</span>UNI</a>
        <a href="landing.php" class="nav-back">← Back to Home</a>
    </nav>

    <!-- PAGE HERO -->
    <header class="page-hero">
        <div class="hero-inner">
            <p class="eyebrow">Est. 2024 · Kathmandu, Nepal</p>
            <h1>The Story Behind Every Plate</h1>
            <p class="lead">Sauni is more than a food delivery platform. It is a living archive of Nepal's culinary identity — built on authentic ingredients, ancestral techniques, and a deep commitment to the communities that carry these traditions forward.</p>
        </div>
    </header>

    <!-- SECTION 1: THE CULTURE -->
    <section class="story-section">
        <div class="section-inner">
            <div class="section-label">
                <span class="num">01</span>
                <span class="cat">The Culture</span>
            </div>
            <div class="section-body">
                <div class="text-block">
                    <h2>Nepal's food is a living history book.</h2>
                    <p>For centuries, food in Nepal has been far more than fuel. It is ceremony. It is identity. From elaborate ritual spreads shared during festivals to slow-simmered regional dishes born from mountain valleys and river plains, every preparation carries the memory of a people, a geography, and a season.</p>
                    <p>Nepal's extraordinary ethnic diversity — over 125 documented groups — has given rise to one of the most varied and underrepresented culinary traditions in Asia. Yet very little of this richness reaches the modern kitchen. Industrial food culture has slowly replaced wood-fired earthen stoves with gas burners, and hand-ground spices with mass-produced packets.</p>
                    <p>Sauni was built in direct opposition to this erosion. We believe that when a recipe disappears, a piece of culture disappears with it. Our platform exists to stop that from happening.</p>
                </div>
                <div class="img-block">
                    <img src="assets/img/culture.png" alt="Nepali culinary culture">
                    <figcaption>Nepal's rich culinary traditions span 125+ ethnic communities, each with distinct ingredients, techniques, and ceremony.</figcaption>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 2: THE CHEFS -->
    <section class="story-section alt">
        <div class="section-inner">
            <div class="section-label">
                <span class="num">02</span>
                <span class="cat">The Chefs</span>
            </div>
            <div class="section-body reverse">
                <div class="text-block">
                    <h2>Historians of taste. Guardians of recipe.</h2>
                    <p>Our partner chefs did not learn their craft in a culinary institute. They learned it standing beside their mothers and grandmothers in smoke-filled village kitchens — watching, absorbing, and slowly inheriting knowledge that was never written down anywhere.</p>
                    <p>Many are home-based artisans, women who have cooked the same recipes for decades with no formal recognition. Sauni gives them a platform, a fair wage, and the dignity of being called what they truly are: culinary masters.</p>
                    <p>Each chef on our network is verified for authenticity. We conduct in-person tastings, cross-reference recipes with community elders, and ensure every method meets the traditional standard. Their hands carry the memory of generations.</p>
                    <blockquote>"The fire is not a cooking tool. It is an ingredient."<cite>— Mina Maharjan, Head Chef, Newari Kitchen</cite></blockquote>
                </div>
                <div class="img-block chef-grid">
                    <div class="chef-imgs">
                        <img src="assets/img/chefs/chef1.png" alt="Chef 1" class="chef-main">
                        <img src="assets/img/chefs/chef2.png" alt="Chef 2" class="chef-sub">
                    </div>
                    <figcaption>Our network of verified artisan chefs — each a guardian of an authentic culinary tradition.</figcaption>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3: THE FOOD -->
    <section class="story-section">
        <div class="section-inner">
            <div class="section-label">
                <span class="num">03</span>
                <span class="cat">The Food</span>
            </div>
            <div class="section-body">
                <div class="text-block">
                    <h2>What you eat is where you come from.</h2>
                    <p>Our menu is not curated by trend or popularity — it is curated by heritage. Every item on our platform is tied to a specific ethnic tradition, a geographic origin, and a seasonal rhythm. We serve what the land actually produces, not what a marketing team decides is appetizing.</p>
                    <p>Behind every dish is a story of the earth it grew from, the hands that prepared it, and the occasion it was made for. Some recipes are reserved for harvest festivals. Others are prepared only during monsoon season. Many have been cooked in the same clay vessels using the same stone methods for over five hundred years.</p>
                    <p>We make sure you hear that story before you take your first bite. Food without context is just calories — Sauni gives you the full picture.</p>
                </div>
                <div class="img-block">
                    <img src="assets/img/foods/allinone.png" alt="Authentic Nepali food spread">
                    <figcaption>A spread of dishes from across Nepal's ethnic communities — each with a distinct origin, ingredient profile, and cultural significance.</figcaption>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4: THE IMPACT -->
    <section class="story-section alt">
        <div class="section-inner">
            <div class="section-label">
                <span class="num">04</span>
                <span class="cat">The Impact</span>
            </div>
            <div class="section-body reverse">
                <div class="text-block">
                    <h2>Every order is a direct investment in a community.</h2>
                    <p>Sauni operates on a zero-middleman model. When you place an order, the revenue flows directly to the home kitchen that cooked it. We charge a transparent platform fee — and the rest goes entirely to the artisan. No distributor markup, no franchise fees, no corporate cut.</p>
                    <p>Our farmers are equally important. We maintain direct partnerships with organic hill farmers across multiple districts. By committing to fixed-price seasonal purchasing, we give these farmers income stability that large market systems never offer.</p>
                    <p>Since launch, Sauni has partnered with over 40 home-based chefs across 8 ethnic communities, onboarded 15 organic farms across 5 districts, and processed hundreds of orders — each one a small act of cultural preservation.</p>
                </div>
                <div class="img-block">
                    <img src="assets/img/respect.png" alt="Community and heritage">
                    <figcaption>The communities behind Sauni — farmers, artisans, and chefs who are the true authors of every dish we deliver.</figcaption>
                </div>
            </div>
        </div>
    </section>

    <!-- PROMISE SECTION -->
    <section class="promise-section">
        <div class="promise-inner">
            <div class="promise-header">
                <span class="num">05</span>
                <h2>Our Promise.</h2>
            </div>
            <ul class="promise-list">
                <li>
                    <strong>Authenticity above all.</strong>
                    If a dish cannot be traced to its ethnic origin with verifiable methods, it does not appear on Sauni.
                </li>
                <li>
                    <strong>Fair compensation, always.</strong>
                    Every chef and every farmer on our network earns above market rate for their contribution.
                </li>
                <li>
                    <strong>Zero compromise on ingredients.</strong>
                    No artificial flavoring, no shortcuts. Everything is sourced fresh, seasonal, and local.
                </li>
                <li>
                    <strong>Cultural documentation.</strong>
                    Every recipe is documented with its origin story, traditional preparation method, and community context.
                </li>
            </ul>
            <a href="customer/browse.php" class="cta-btn">Start Your Order</a>
        </div>
    </section>

    <footer class="page-footer">
        <div class="footer-inner">
            <div class="f-left">
                <div class="f-logo">S🔥UNI</div>
                <p>Preserving Nepal's culinary heritage, one order at a time.</p>
            </div>
            <div class="f-right">
                <p>© 2026 The Sauni Initiative</p>
                <p>Kathmandu, Nepal</p>
            </div>
        </div>
    </footer>

</body>
</html>
