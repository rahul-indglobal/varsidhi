# Deployment Notes - Promotion Bar
**Date Created:** 30 March 2026

### 1. Manual Admin Setup Required
Before this code works, you MUST create the following Static Block:
- **Block Title:** Promotion Announcement Bar
- **Identifier:** promo_announcement_bar
- **Status:** Enabled
- **Store View:** All Store Views

### 2. Content (HTML)
Copy and paste the HTML into the content area of the Static Block:

### 3. New Logo should be updated from admin panel
Go to Content > Design > Configuration > Edit (for the desired store view) > Header > Logo Image and upload the new logo.

### 4. Slider Setup
MUST create the following Static Block:
- **Block Title:** Home Hero Slider
- **Identifier:** home_hero_slider
- **Status:** Enabled
- **Store View:** All Store Views

Must Create the following Widget:
- **Widget Title:** Homepage Main Slider
- 
### 5. Create Product Attribute
- **Attribute Title:** Product Ribbon
- **Attribute Code:** product_ribbon
- **Attribute Type:** TEXT
- **Assign Attribute to ALL Attribute Set**
### 6. Update: CMS Homepage Configuration

## Summary
Removed the hardcoded template directive from the CMS Home Page to clean up legacy rendering and prepare for the new layout structure.

## Changes
- **Page Title:** Homepage
- **Page ID:** `16`
- **Action:** Removed the following block directive from the Content area:
  ```html
  {{block class="Magento\Framework\View\Element\Template" template="Magento_Theme::html/homepage.phtml"}}
