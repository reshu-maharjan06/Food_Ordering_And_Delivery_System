# AI Chatbot for Menu Suggestions - Research & Planning Document

**Project:** AI_Foodbot - Food Ordering and Delivery System  
**Feature:** AI-Powered Smart Menu Assistant  
**Date:** May 1, 2026  
**Status:** Research & Planning Phase

---

## 1. EXECUTIVE SUMMARY

This document outlines the comprehensive research, analysis, and development plan for enhancing the AI Chatbot system with intelligent menu suggestion capabilities. The chatbot will help customers discover relevant menu items based on their preferences, dietary restrictions, budget, and previous order history.

---

## 2. CURRENT STATE ANALYSIS

### 2.1 Existing Implementation
✅ **What's Already Working:**
- Gemini AI API integration (gemini-2.5-flash with fallback to gemini-2.0-flash)
- Rate limiting (10 requests per 60 seconds)
- Real-time menu fetching from database
- Smart prompt engineering with budget constraints
- Quick suggestion buttons (Popular, Budget, Newari cuisine)
- Frontend chat UI with message history
- Quick Add to Cart functionality
- Dish card rendering with images and pricing
- Error handling and API fallback mechanisms

### 2.2 Technology Stack
- **Backend:** PHP 7.4+ with PDO (MySQL)
- **Frontend:** Vanilla JavaScript (no framework dependencies)
- **AI Provider:** Google Generative AI (Gemini)
- **Database:** MySQL with menu_item, category, cuisine_type tables
- **API:** RESTful (api.php with action parameters)

### 2.3 Project Structure
```
Ai_Foodbot/
├── api.php (AI chat endpoint)
├── includes/
│   ├── config.php (API keys & models)
│   ├── db.php (Database connection)
│   ├── env.php (Environment loader)
│   └── security.php (Security utilities)
├── assets/
│   └── js/customer/
│       └── assistant.js (Frontend chatbot logic)
├── scratch/ (Testing files)
└── .env (Local configuration)
```

---

## 3. FEATURE RESEARCH & ANALYSIS

### 3.1 Menu Suggestion Algorithm
The AI chatbot analyzes user queries using the following approach:
```
User Input → Filter by Budget → Filter by Category → Filter by Cuisine Type → 
Filter by Availability → Apply AI Ranking → Return Top 3 Recommendations
```

### 3.2 Current Capabilities
1. **Budget-based filtering** - "Under 300", "Cheap eats"
2. **Cuisine-based suggestions** - "Newari food", "Chinese", etc.
3. **Trend-based recommendations** - "Popular items"
4. **Constraint handling** - Price limits, dietary preferences
5. **Quick ordering** - Direct add-to-cart from suggestions

### 3.3 Limitations Identified
❌ **Areas for Enhancement:**
1. No personalization based on user history
2. No dietary restrictions tracking (vegetarian, gluten-free, etc.)
3. Limited context understanding for complex queries
4. No order history analysis
5. No review/rating integration
6. Single-turn conversation (limited multi-turn dialogue)
7. No seasonal/promotional menu awareness
8. No smart time-based recommendations (breakfast vs dinner)

---

## 4. ENHANCEMENT ROADMAP

### Phase 1: Core Improvements (Week 1)
**Branch:** `feature/ai-chatbot-v2-core`

#### 4.1.1 Enhanced User Profiling
```php
// New table: user_preferences
- user_id
- dietary_restrictions (JSON: vegetarian, vegan, gluten_free)
- cuisine_preferences (JSON array)
- spice_level (mild, medium, hot)
- budget_range_min/max
- allergens (JSON array)
```

#### 4.1.2 Improved Prompt Engineering
- Add user history context
- Include dietary restrictions in AI instructions
- Add allergy warnings
- Provide nutritional information when available
- Include customer reviews in recommendations

#### 4.1.3 Multi-turn Conversation Support
- Implement conversation state management
- Track dialogue context
- Enable follow-up questions
- Maintain user preferences across sessions

**Sample Code:**
```php
// api.php enhancement
$conversation_context = get_user_conversation_history($userId, limit: 5);
$user_preferences = fetch_user_preferences($userId);

$system_instructions .= "USER HISTORY:\n" . format_history($conversation_context);
$system_instructions .= "USER PREFERENCES:\n" . json_encode($user_preferences);
```

### Phase 2: Personalization & Analytics (Week 2)
**Branch:** `feature/ai-chatbot-v2-personalization`

#### 4.2.1 Smart Recommendations Engine
```php
// New features:
- Order frequency analysis
- Favorite dish tracking
- Repeat order predictions
- Cross-sell recommendations
- Seasonal item awareness
- Time-based menu optimization (breakfast, lunch, dinner)
```

#### 4.2.2 Analytics & Tracking
```php
// New table: chatbot_interactions
- user_id
- query
- suggestions_provided
- click_through_rate
- order_conversion
- timestamp
```

#### 4.2.3 Feedback Loop
```javascript
// Frontend enhancement
- Rating system for suggestions (👍/👎)
- "Why I didn't like this" feedback
- Manual correction interface
- Conversation quality feedback
```

### Phase 3: Advanced Features (Week 3)
**Branch:** `feature/ai-chatbot-v2-advanced`

#### 4.3.1 Natural Language Processing Improvements
- Sentiment analysis for user satisfaction
- Intent classification (recommendations, complaints, inquiries)
- Entity extraction (cuisine, price, ingredients)
- Spell correction and typo handling

#### 4.3.2 Image-based Menu Understanding
- OCR for menu images
- Visual similarity search
- Food image recognition

#### 4.3.3 Integration with Delivery Optimization
- Delivery time estimation
- Bundle deals awareness
- Stock availability sync
- Real-time pricing updates

---

## 5. TECHNICAL IMPLEMENTATION DETAILS

### 5.1 Database Schema Additions

```sql
-- User Preferences
CREATE TABLE user_preferences (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    dietary_restrictions JSON,
    cuisine_preferences JSON,
    spice_level ENUM('mild', 'medium', 'hot') DEFAULT 'medium',
    budget_range_min INT DEFAULT 0,
    budget_range_max INT DEFAULT 5000,
    allergens JSON,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Conversation History
CREATE TABLE chatbot_interactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_id VARCHAR(255),
    query TEXT NOT NULL,
    ai_response LONGTEXT,
    suggested_items JSON,
    user_feedback ENUM('positive', 'neutral', 'negative'),
    clicked_item_id INT,
    order_created BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (user_id, created_at),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Menu Item Reviews for AI Context
CREATE TABLE menu_item_reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    menu_item_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (menu_item_id, rating),
    FOREIGN KEY (menu_item_id) REFERENCES menu_item(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### 5.2 API Endpoint Enhancements

```php
// Current: api.php?action=ai_chat
// Enhanced endpoints:
- api.php?action=ai_chat (existing, enhanced)
- api.php?action=save_preferences (new)
- api.php?action=get_preferences (new)
- api.php?action=rate_suggestion (new)
- api.php?action=get_conversation_history (new)
- api.php?action=smart_recommendations (new)
```

### 5.3 Frontend Enhancement Strategy

**Current assistant.js improvements:**
```javascript
// Add conversation state management
const ChatState = {
    conversationId: null,
    userPreferences: {},
    history: [],
    
    savePreferences(prefs) { /* ... */ },
    trackInteraction(suggestion, result) { /* ... */ },
    rateSuggestion(suggestionId, rating) { /* ... */ }
};

// Enhanced message rendering
function renderSuggestionCard(item, metadata) {
    // Show why item was recommended
    // Display reviews from other users
    // Show nutritional info
    // Allergen warnings
}
```

---

## 6. SECURITY CONSIDERATIONS

✅ **Already Implemented:**
- Rate limiting (10/60s per user)
- Session-based authentication
- Input validation through AI model

🔒 **Additional Security Measures:**
1. **Input sanitization** for user preferences
2. **SQL injection prevention** in preference queries
3. **XSS protection** in frontend rendering
4. **CSRF tokens** for preference updates
5. **Data privacy** - encrypt sensitive preferences
6. **Audit logging** for AI interactions

```php
// Security example
define('MAX_QUERY_LENGTH', 500);
define('ALLOWED_DIETARY_RESTRICTIONS', ['vegetarian', 'vegan', 'gluten_free', 'dairy_free']);

function validate_preferences($prefs) {
    $restrictions = json_decode($prefs['restrictions'] ?? '[]', true);
    foreach ($restrictions as $r) {
        if (!in_array($r, ALLOWED_DIETARY_RESTRICTIONS)) {
            throw new Exception('Invalid dietary restriction');
        }
    }
    return true;
}
```

---

## 7. TESTING STRATEGY

### 7.1 Unit Tests
- Preference validation functions
- Budget filtering logic
- Prompt generation
- Response parsing

### 7.2 Integration Tests
- Database operations (CRUD for preferences)
- API endpoint functionality
- AI model integration
- Rate limiting verification

### 7.3 User Acceptance Tests
- Recommendation accuracy
- Response time (<3 seconds)
- Mobile responsiveness
- Accessibility compliance

### 7.4 Performance Tests
- AI response latency
- Database query optimization
- Caching strategy for menu data
- Concurrent user handling

**Testing Framework:** PHPUnit, Jest (for frontend)

---

## 8. DEPLOYMENT PLAN

### 8.1 Branch Strategy
```
main (production)
  └── develop (staging)
      ├── feature/ai-chatbot-v2-core (Phase 1)
      ├── feature/ai-chatbot-v2-personalization (Phase 2)
      └── feature/ai-chatbot-v2-advanced (Phase 3)
```

### 8.2 Release Timeline
- **Week 1:** Phase 1 (Core improvements)
- **Week 2:** Phase 2 (Personalization)
- **Week 3:** Phase 3 (Advanced features)
- **Week 4:** Testing & Bug fixes
- **Week 5:** Production deployment

### 8.3 Rollback Strategy
- Database migration rollback scripts
- Feature flags for gradual rollout
- A/B testing for recommendation algorithms
- User feedback monitoring

---

## 9. SUCCESS METRICS

📊 **Key Performance Indicators:**

| Metric | Current | Target | Timeline |
|--------|---------|--------|----------|
| Avg Response Time | <2s | <1.5s | Week 2 |
| Suggestion Click-through Rate | ~45% | >60% | Week 3 |
| Order Conversion from Chat | ~30% | >50% | Week 4 |
| User Satisfaction Score | 3.8/5 | 4.5/5 | Week 4 |
| Repeat User Engagement | ~40% | >70% | End of Month |
| API Availability | 99.5% | 99.9% | Week 3 |

---

## 10. TEAM REQUIREMENTS

### 10.1 Skillsets Needed
- **Backend Developer:** PHP, MySQL optimization, API design
- **Frontend Developer:** JavaScript, UX/UI optimization
- **AI/ML Engineer:** Prompt engineering, LLM fine-tuning
- **QA Engineer:** Test automation, performance testing
- **DevOps:** Database migrations, deployment automation

### 10.2 Tools & Resources
- Postman (API testing)
- PHPMyAdmin (Database management)
- Git workflow tools
- Google Gemini API documentation
- Analytics dashboard (for tracking metrics)

---

## 11. RISK ANALYSIS & MITIGATION

| Risk | Likelihood | Impact | Mitigation |
|------|-----------|--------|-----------|
| AI response hallucination | Medium | High | Strict menu item validation, human review |
| High API costs (Gemini) | Medium | Medium | Implement caching, rate limiting, model optimization |
| Database performance degradation | Low | High | Query optimization, indexing, archival strategy |
| User data privacy concerns | Low | High | Encryption, compliance audit, clear privacy policy |
| Mobile responsiveness issues | Low | Medium | Responsive testing on various devices |
| Recommendation accuracy | Medium | Medium | A/B testing, feedback loop, continuous training |

---

## 12. NEXT STEPS

1. ✅ **Create feature branches** for each phase
2. ✅ **Setup database** with new tables
3. ✅ **Implement Phase 1** (Core improvements)
4. ✅ **Write comprehensive tests**
5. ✅ **Performance optimization**
6. ✅ **User acceptance testing**
7. ✅ **Documentation & deployment**

---

## 13. RESOURCES & REFERENCES

### Official Documentation
- [Google Generative AI (Gemini) API](https://ai.google.dev/tutorials/rest_quickstart)
- [PHP PDO Documentation](https://www.php.net/manual/en/book.pdo.php)
- [Food Ordering System Best Practices](https://github.com/reshu-maharjan06/Food_Ordering_And_Delivery_System)

### Related Projects
- Original Food Ordering System: https://github.com/reshu-maharjan06/Food_Ordering_And_Delivery_System.git
- Reference Implementation: Current Ai_Foodbot project

### Learning Resources
- Prompt Engineering Best Practices
- Database Optimization Techniques
- REST API Design Patterns
- JavaScript Asynchronous Programming

---

## 14. APPENDIX

### A. Sample Queries

**Get personalized recommendations:**
```sql
SELECT m.*, 
       COUNT(oi.id) as order_frequency,
       AVG(r.rating) as avg_rating
FROM menu_item m
LEFT JOIN order_items oi ON m.id = oi.menu_item_id AND oi.user_id = ?
LEFT JOIN menu_item_reviews r ON m.id = r.menu_item_id
WHERE m.category_id IN (
    SELECT CAST(JSON_UNQUOTE(JSON_EXTRACT(cuisine_preferences, CONCAT('$[', idx, ']'))) AS INT)
    FROM user_preferences
    WHERE user_id = ?
)
GROUP BY m.id
ORDER BY order_frequency DESC, avg_rating DESC
LIMIT 5;
```

### B. Prompt Engineering Template

```
You are FoodBot, a smart food recommendation assistant.

USER PROFILE:
- Dietary Restrictions: {restrictions}
- Preferred Cuisines: {cuisines}
- Spice Level: {spice_level}
- Budget: Rs {budget_min} - Rs {budget_max}
- Allergens: {allergens}

AVAILABLE MENU:
{menu_items_with_reviews}

USER REQUEST:
{user_query}

RESPONSE GUIDELINES:
1. Only recommend items from the available menu
2. Respect budget and dietary restrictions strictly
3. Include 2-3 top recommendations with brief explanations
4. Wrap item names in [brackets] for quick add-to-cart
5. Mention why each item matches user preferences
6. Include allergy warnings if applicable
```

---

**Document Version:** 1.0  
**Last Updated:** May 1, 2026  
**Author:** Development Team  
**Status:** Ready for Implementation
