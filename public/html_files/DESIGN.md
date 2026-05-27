---
name: Industrial Tech-Forward Narrative
colors:
  surface: '#f6fafd'
  surface-dim: '#d6dbdd'
  surface-bright: '#f6fafd'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f0f4f7'
  surface-container: '#eaeef1'
  surface-container-high: '#e5e9ec'
  surface-container-highest: '#dfe3e6'
  on-surface: '#171c1f'
  on-surface-variant: '#44474d'
  inverse-surface: '#2c3134'
  inverse-on-surface: '#edf1f4'
  outline: '#74777d'
  outline-variant: '#c4c6cd'
  surface-tint: '#4d6079'
  primary: '#000917'
  on-primary: '#ffffff'
  primary-container: '#0d2137'
  on-primary-container: '#7689a4'
  inverse-primary: '#b5c8e5'
  secondary: '#00677c'
  on-secondary: '#ffffff'
  secondary-container: '#4cd9fd'
  on-secondary-container: '#005d6f'
  tertiary: '#000a0e'
  on-tertiary: '#ffffff'
  tertiary-container: '#00242d'
  on-tertiary-container: '#3792aa'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d2e4ff'
  primary-fixed-dim: '#b5c8e5'
  on-primary-fixed: '#081c32'
  on-primary-fixed-variant: '#364860'
  secondary-fixed: '#b1ecff'
  secondary-fixed-dim: '#48d7fb'
  on-secondary-fixed: '#001f27'
  on-secondary-fixed-variant: '#004e5e'
  tertiary-fixed: '#b0ecff'
  tertiary-fixed-dim: '#7dd2eb'
  on-tertiary-fixed: '#001f27'
  on-tertiary-fixed-variant: '#004e5e'
  background: '#f6fafd'
  on-background: '#171c1f'
  surface-variant: '#dfe3e6'
typography:
  display-lg:
    fontFamily: Syne
    fontSize: 64px
    fontWeight: '800'
    lineHeight: '1.1'
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Syne
    fontSize: 40px
    fontWeight: '800'
    lineHeight: '1.2'
    letterSpacing: -0.01em
  headline-xl:
    fontFamily: Syne
    fontSize: 48px
    fontWeight: '700'
    lineHeight: '1.2'
  headline-lg:
    fontFamily: Syne
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.3'
  headline-md:
    fontFamily: Syne
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.4'
  body-lg:
    fontFamily: DM Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: DM Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.5'
  label-md:
    fontFamily: DM Sans
    fontSize: 14px
    fontWeight: '500'
    lineHeight: '1.2'
    letterSpacing: 0.05em
  code-snippet:
    fontFamily: DM Sans
    fontSize: 13px
    fontWeight: '500'
    lineHeight: '1.5'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 64px
  container-max: 1440px
---

## Brand & Style

The design system is engineered to evoke "Industrial Reliability meets Cutting-Edge Innovation." It bridges the gap between heavy-duty consultancy and high-velocity technology. The aesthetic, titled "Built From Here," emphasizes structural integrity through visible grid patterns, technical precision, and high-fidelity motion.

The style is a sophisticated blend of **Corporate Modernism** and **Glassmorphism**, accented by industrial "data-stream" motifs. It aims to evoke a sense of absolute competence, forward-looking vision, and architectural stability. The UI feels less like a simple website and more like a high-end technical dashboard or an aerospace interface.

## Colors

The palette is anchored in a professional, authoritative Deep Navy that provides high-contrast grounding for technical content. 

- **Primary (Deep Navy):** Used for structural elements, heavy typography, and high-importance backgrounds.
- **Secondary (Vibrant Cyan):** The "action" color. Used for interactive states, key accents, and to represent "live" data streams or radar sweeps.
- **Tertiary (Sky Blue):** Used for supporting information, softer highlights, and secondary UI elements.
- **Neutral (Ice Blue):** Provides a crisp, tech-forward alternative to standard grays, serving as the primary background and container surface.

Backgrounds should lean heavily on the Ice Blue to maintain a "clean-room" feel, while Deep Navy is reserved for headers and footer sections to provide a strong visual frame.

## Typography

This design system utilizes a high-contrast typographic pairing. **Syne** is used for headlines to provide a distinctive, wide-set technical character that feels avant-garde yet sturdy. **DM Sans** provides a highly legible, geometric companion for body text and labels, ensuring that complex consultancy data remains accessible.

All uppercase labels should utilize the `label-md` style with increased letter spacing to enhance the technical "blueprint" aesthetic. Headlines should prioritize tight leading to maintain a cohesive, blocky structure.

## Layout & Spacing

The layout is built on a **12-column fluid grid** for desktop, shifting to a **4-column grid** for mobile. The design system emphasizes structural visibility; where possible, use subtle 1px border dividers in `Ice Blue` or `Sky Blue` to delineate grid sections.

- **Rhythm:** A strict 8px base unit drives all padding and margin decisions.
- **Sectioning:** Content is grouped into "modules" with significant vertical padding (80px–120px) to allow the tech-forward visuals room to breathe.
- **Patterns:** Use a subtle 32px x 32px dot matrix or grid line overlay on background sections to reinforce the "Built From Here" industrial theme.

## Elevation & Depth

Hierarchy is established through **Glassmorphism** and tonal layering rather than traditional heavy shadows. 

1.  **Base Layer:** The Ice Blue (`#F4F8FB`) solid background.
2.  **Surface Layer:** White or translucent surfaces with a 12px-20px backdrop blur (Glassmorphism). These should have a subtle 1px inner stroke using a lighter tint of the secondary color.
3.  **Active Layer:** Deep Navy components that appear to "anchor" the UI, providing the highest contrast.
4.  **Floating Elements:** Interactive chips and tooltips use a very soft, diffused `Deep Navy` shadow (opacity 4%) to suggest a slight lift without breaking the clean, technical aesthetic.

Incorporate "Radar Sweep" animations—a subtle gradient light passing across glass surfaces—to indicate loading states or section transitions.

## Shapes

The design system utilizes **Rounded** (Level 2) geometry. A 0.5rem (8px) radius is the standard for most containers and buttons. This strikes a balance between the aggressive sharpness of industrial tech and the modern approachable nature of high-end consulting.

- **Standard Elements:** 8px (0.5rem)
- **Large Cards/Containers:** 16px (1rem)
- **Small Interactive Elements (Chips/Tags):** 24px (1.5rem) for a pill-shaped appearance to contrast against the rigid grid.

## Components

### Buttons
Primary buttons are high-contrast Deep Navy with white text, or Vibrant Cyan with Deep Navy text for urgent CTAs. They feature a sharp hover state where the background fills or shifts with a technical "glitch" or "slide" transition.

### Glassmorphic Cards
Cards use a semi-transparent white background with a heavy backdrop blur. They must include a 1.5px border in a low-opacity `Sky Blue` to define their edges against the grid.

### Input Fields
Inputs are structured with a solid Ice Blue background and a 1px border. On focus, the border transitions to Vibrant Cyan, and a subtle "scan-line" animation may momentarily pass through the field.

### Technical Infographics
Data visualizations should utilize the full palette, using `Vibrant Cyan` for the most critical data points. Use "data-stream" particle effects (small moving dots on lines) to represent flow or connectivity within diagrams.

### Navigation
The header should be a fixed glassmorphic bar. Nav items use `label-md` typography with an underline "underline" animation that tracks the movement of the mouse, mimicking a radar scan.