# Q-Pharmacy Design System

## Overview

Design System Q-Pharmacy adalah panduan komprehensif untuk memastikan konsistensi visual dan pengalaman pengguna di seluruh aplikasi. Sistem ini dibangun di atas Quasar Framework dengan Material Design principles.

## Table of Contents

1. [Color Palette](#color-palette)
2. [Typography](#typography)
3. [Spacing System](#spacing-system)
4. [Grid System](#grid-system)
5. [Elevation & Shadows](#elevation--shadows)
6. [Border Radius](#border-radius)
7. [Breakpoints](#breakpoints)
8. [Implementation Guidelines](#implementation-guidelines)

## Color Palette

### Primary Colors

```scss
// Primary Brand Colors
$primary: #1976D2;     // Blue - Main brand color
$secondary: #26A69A;   // Teal - Secondary actions
$accent: #9C27B0;      // Purple - Accent elements

// Primary Variants
$primary-light: #42A5F5;
$primary-dark: #1565C0;
$primary-contrast: #FFFFFF;
```

### Status Colors

```scss
// Status & Feedback Colors
$positive: #21BA45;    // Success states
$negative: #C10015;    // Error states
$info: #31CCEC;        // Information
$warning: #F2C037;     // Warning states
```

### Neutral Colors

```scss
// Neutral Palette
$dark: #1D1D1D;        // Primary text
$dark-page: #121212;   // Dark theme background
$grey-1: #F5F5F5;      // Light backgrounds
$grey-2: #EEEEEE;      // Borders, dividers
$grey-3: #E0E0E0;      // Disabled states
$grey-4: #BDBDBD;      // Secondary text
$grey-5: #9E9E9E;      // Placeholder text
$grey-6: #757575;      // Icons
$grey-7: #616161;      // Body text
$grey-8: #424242;      // Headlines
$grey-9: #212121;      // Primary text
$grey-10: #000000;     // Pure black
```

### Semantic Colors

```scss
// Pharmacy-specific Colors
$medicine-primary: #4CAF50;    // Medicine/health related
$medicine-secondary: #81C784;  // Light medicine color
$prescription: #FF9800;        // Prescription related
$inventory: #2196F3;           // Inventory management
$sales: #9C27B0;              // Sales/revenue
$customer: #00BCD4;           // Customer related
```

## Typography

### Font Family

```scss
// Primary Font Stack
$typography-font-family: 'Roboto', '-apple-system', 'Helvetica Neue', Helvetica, Arial, sans-serif;

// Monospace for code/numbers
$typography-code-family: 'Roboto Mono', Consolas, 'Liberation Mono', Courier, monospace;
```

### Font Weights

```scss
$font-weight-thin: 100;
$font-weight-light: 300;
$font-weight-regular: 400;
$font-weight-medium: 500;
$font-weight-bold: 700;
$font-weight-bolder: 900;
```

### Font Sizes & Line Heights

```scss
// Heading Scales
$h1-size: 2.5rem;      // 40px
$h2-size: 2rem;        // 32px
$h3-size: 1.75rem;     // 28px
$h4-size: 1.5rem;      // 24px
$h5-size: 1.25rem;     // 20px
$h6-size: 1rem;        // 16px

// Body Text
$body-font-size: 0.875rem;     // 14px
$body-line-height: 1.5;

// Small Text
$caption-font-size: 0.75rem;   // 12px
$overline-font-size: 0.625rem; // 10px

// Line Heights
$line-height-xs: 1.2;
$line-height-sm: 1.4;
$line-height-md: 1.5;
$line-height-lg: 1.6;
$line-height-xl: 1.8;
```

### Typography Classes

```scss
// Utility Classes
.text-h1 { font-size: $h1-size; font-weight: $font-weight-light; }
.text-h2 { font-size: $h2-size; font-weight: $font-weight-regular; }
.text-h3 { font-size: $h3-size; font-weight: $font-weight-regular; }
.text-h4 { font-size: $h4-size; font-weight: $font-weight-regular; }
.text-h5 { font-size: $h5-size; font-weight: $font-weight-regular; }
.text-h6 { font-size: $h6-size; font-weight: $font-weight-medium; }

.text-subtitle1 { font-size: 1rem; font-weight: $font-weight-regular; }
.text-subtitle2 { font-size: 0.875rem; font-weight: $font-weight-medium; }
.text-body1 { font-size: 1rem; font-weight: $font-weight-regular; }
.text-body2 { font-size: 0.875rem; font-weight: $font-weight-regular; }
.text-caption { font-size: $caption-font-size; font-weight: $font-weight-regular; }
.text-overline { font-size: $overline-font-size; font-weight: $font-weight-regular; text-transform: uppercase; }
```

## Spacing System

### Base Spacing Unit

```scss
// Base unit: 4px
$space-base: 4px;

// Spacing Scale (based on 4px grid)
$space-none: 0;
$space-xs: $space-base;        // 4px
$space-sm: $space-base * 2;    // 8px
$space-md: $space-base * 3;    // 12px
$space-lg: $space-base * 4;    // 16px
$space-xl: $space-base * 6;    // 24px
$space-2xl: $space-base * 8;   // 32px
$space-3xl: $space-base * 12;  // 48px
$space-4xl: $space-base * 16;  // 64px
$space-5xl: $space-base * 20;  // 80px
```

### Component Spacing

```scss
// Component-specific spacing
$component-padding-xs: $space-sm;   // 8px
$component-padding-sm: $space-md;   // 12px
$component-padding-md: $space-lg;   // 16px
$component-padding-lg: $space-xl;   // 24px
$component-padding-xl: $space-2xl;  // 32px

// Layout spacing
$layout-gutter: $space-lg;          // 16px
$layout-margin: $space-xl;          // 24px
$section-spacing: $space-3xl;       // 48px
```

## Grid System

### Breakpoints

```scss
$breakpoint-xs: 0;
$breakpoint-sm: 600px;
$breakpoint-md: 1024px;
$breakpoint-lg: 1440px;
$breakpoint-xl: 1920px;
```

### Container Widths

```scss
$container-max-widths: (
  sm: 540px,
  md: 720px,
  lg: 960px,
  xl: 1140px
);
```

## Elevation & Shadows

```scss
// Material Design Elevation
$shadow-1: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
$shadow-2: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23);
$shadow-3: 0 10px 20px rgba(0,0,0,0.19), 0 6px 6px rgba(0,0,0,0.23);
$shadow-4: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
$shadow-5: 0 19px 38px rgba(0,0,0,0.30), 0 15px 12px rgba(0,0,0,0.22);

// Component shadows
$card-shadow: $shadow-1;
$modal-shadow: $shadow-4;
$dropdown-shadow: $shadow-2;
```

## Border Radius

```scss
// Border radius scale
$border-radius-xs: 2px;
$border-radius-sm: 4px;
$border-radius-md: 6px;
$border-radius-lg: 8px;
$border-radius-xl: 12px;
$border-radius-2xl: 16px;
$border-radius-full: 50%;

// Component radius
$button-border-radius: $border-radius-sm;
$card-border-radius: $border-radius-lg;
$input-border-radius: $border-radius-sm;
```

## Implementation Guidelines

### 1. Using Design Tokens

```vue
<template>
  <div class="pharmacy-card">
    <h3 class="text-h5 q-mb-md">Medicine Information</h3>
    <p class="text-body2 text-grey-7">Description text</p>
  </div>
</template>

<style lang="scss" scoped>
.pharmacy-card {
  padding: $component-padding-lg;
  border-radius: $card-border-radius;
  box-shadow: $card-shadow;
  background: white;
}
</style>
```

### 2. Color Usage Guidelines

- **Primary**: Main actions, navigation, brand elements
- **Secondary**: Secondary actions, complementary elements
- **Accent**: Call-to-action buttons, highlights
- **Positive**: Success messages, confirmations
- **Negative**: Errors, warnings, destructive actions
- **Medicine Colors**: Health-related features, medicine categories

### 3. Typography Hierarchy

```vue
<!-- Page Title -->
<h1 class="text-h3 text-weight-light q-mb-lg">Dashboard</h1>

<!-- Section Title -->
<h2 class="text-h5 text-weight-medium q-mb-md">Recent Orders</h2>

<!-- Card Title -->
<h3 class="text-h6 q-mb-sm">Order #12345</h3>

<!-- Body Text -->
<p class="text-body2 text-grey-7">Order details...</p>

<!-- Caption -->
<span class="text-caption text-grey-5">Last updated 2 hours ago</span>
```

### 4. Spacing Best Practices

```vue
<template>
  <div class="page-container">
    <!-- Use consistent spacing classes -->
    <div class="q-pa-lg">        <!-- Large padding -->
      <div class="q-mb-xl">     <!-- Extra large margin bottom -->
        <h1 class="text-h4">Title</h1>
      </div>
      
      <div class="q-gutter-md">  <!-- Medium gutter between children -->
        <q-card class="q-pa-md"> <!-- Medium padding -->
          Content
        </q-card>
      </div>
    </div>
  </div>
</template>
```

### 5. Responsive Design

```vue
<template>
  <div class="responsive-grid">
    <div class="row q-gutter-md">
      <div class="col-12 col-md-6 col-lg-4">
        <!-- Responsive columns -->
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.responsive-grid {
  @media (max-width: $breakpoint-sm) {
    padding: $space-md;
  }
  
  @media (min-width: $breakpoint-md) {
    padding: $space-xl;
  }
}
</style>
```

## Cross-References

- [UI/UX Guidelines](./UI_UX_GUIDELINES.md) - Navigation patterns and user journeys
- [Accessibility Guidelines](./ACCESSIBILITY.md) - WCAG compliance and inclusive design
- [Component Style Guide](../frontend/web/docs/COMPONENTS.md) - Component usage examples
- [Implementation Roadmap](../ROADMAP.md) - Timeline and priorities

## Maintenance

Design system ini harus diperbarui secara berkala untuk memastikan konsistensi dan relevansi. Setiap perubahan harus:

1. Didokumentasikan dengan jelas
2. Dikomunikasikan kepada tim development
3. Diimplementasikan secara bertahap
4. Diuji pada berbagai komponen

---

**Last Updated**: September 2025  
**Version**: 0.0.1  
**Maintainer**: Q-Pharmacy Development Team