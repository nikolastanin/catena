# Slots Plugin Documentation

## Setup Process

### Installing the Plugin

1. Download zip
2. Install & Activate the plugin
3. Check settings>permalinks (`/wp-admin/options-permalink.php`) is set to "Post Name" option

### Adding Slot Games

1. Go to: `/wp-admin/edit.php?post_type=slot`
2. Click on "Add new Slot"
3. Fill out the backend fields (Image is not needed)
4. (optional) Add this shortcode to the Slot content: `[slot_detail]`
   - If you don't add it to the content, we will still render the shortcode by using a hacky content filter `filter_slot_content()` to save editor time adding the shortcode manually
5. Publish the slot

### Create a Slot Grid (Listing) Page

1. Create a post or page
2. **(!!!)** Add this shortcode to the content: `[slots_grid]`

Now, if followed all steps, you should be able to view the basic slots grid.

## Style/Markup/Features Customization

### Modifying Markup for the Grid

**Location:** `/wp-admin/admin.php?page=slots-settings#slot_editor_markup`

There is an option in the backend to customize the markup and styling for the cards that appear in the **grid**. The backend field has placeholder reusable variables that the plugin will use to render dynamic data. Like `{{slot_id}}` `{{slot_title}}` etc.

#### Example Slot Card Template

Use this example HTML to style the grid uniquely. I basically prompted ChatGPT to preserve the placeholder variables like `{{slot_title}}`, and create a neon-like slot design using Tailwind classes.

```html
<!-- CARDS IN THE GRID  -->
<article
  class="slot-card group relative overflow-hidden rounded-2xl border-[3px] border-cyan-300 bg-[radial-gradient(120%_120%_at_0%_0%,_#131735_0%,_#0a0d22_55%,_#060814_100%)] shadow-[0_0_22px_rgba(41,211,255,0.35),inset_0_0_30px_rgba(0,0,0,0.6)]"
  data-slot-id="{{slot_id}}"
>
  <!-- scanline overlay -->
  <div aria-hidden="true" class="pointer-events-none absolute inset-0 z-0 rounded-2xl bg-[repeating-linear-gradient(to_bottom,rgba(255,255,255,0.04)_0,rgba(255,255,255,0.04)_1px,transparent_2px,transparent_4px)] mix-blend-overlay opacity-20"></div>

  <!-- Image / Hero (full width) -->
  <a href="{{slot_permalink}}" title="{{slot_title}}" class="relative block overflow-hidden border-b-2 border-fuchsia-500">
    <div class="aspect-[16/9] sm:aspect-[21/9]">
      <img
        src="{{slot_image}}"
        alt="{{slot_title}}"
        loading="lazy"
        class="h-full w-full object-cover transition-transform duration-300 ease-out group-hover:scale-[1.03]"
      />
    </div>
    <!-- ... rest of code ... -->
  </a>

  <!-- Content -->
  <div class="relative z-10 flex flex-col gap-3 p-5 sm:p-6 text-slate-100">
    <!-- Title -->
    <h3 class="text-lg font-semibold tracking-tight">
      <a href="{{slot_permalink}}" title="{{slot_title}}" class="text-[#8be9ff]">
        {{slot_title}}
      </a>
    </h3>
    <!-- ... rest of code ... -->
  </div>
</article>
```

After adding that new markup, saving and enabling the checkbox, the grid will have the new neon-style design.

### Single Slot Page Markup

**Location:** `/wp-admin/admin.php?page=slots-settings#slot_card_template`

Similarly like the field above, we can edit the single custom post type view.

#### Example HTML for Single Slot Page

Again prompted ChatGPT for this to alter the vibe:

```html
<!-- SINGLE SLOT CPT Page-->
<div class="slot-detail-page min-h-[100svh] m-0 p-6 font-sans text-slate-100 bg-[radial-gradient(120%_120%_at_0%_0%,_#131735_0%,_#0a0d22_55%,_#060814_100%)]">
  <div class="slot-detail-container relative mx-auto max-w-[1000px] overflow-hidden rounded-[18px] border-[3px] border-cyan-300 bg-[#0c0f25] p-5">
    
    <!-- CRT scanlines overlay -->
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 z-0 rounded-[18px]"></div>

    <!-- Header (image full width, details below) -->
    <div class="slot-detail-header relative z-10 flex flex-col items-stretch gap-5">
      
      <!-- Main image: now full width -->
      <div class="slot-detail-image relative w-full overflow-hidden rounded-xl border-[3px] border-fuchsia-500">
        <img src="{{slot_image}}" alt="{{slot_title}}" class="slot-detail-main-image">
        <!-- ... rest of code ... -->
      </div>

      <!-- Info panel -->
      <div class="slot-detail-info w-full min-w-0 flex flex-col gap-3">
        <div class="slot-detail-title">{{slot_title}}</div>
        <!-- ... rest of code ... -->
      </div>
    </div>

    <!-- Description -->
    <div class="slot-detail-description">
      {{slot_description}}
    </div>

    <!-- Actions -->
    <div class="slot-detail-actions">
      <a href="{{slot_permalink}}" class="slot-detail-button primary">
        ▶ Play Now 🎮
      </a>
      <!-- ... rest of code ... -->
    </div>
  </div>
</div>
```



