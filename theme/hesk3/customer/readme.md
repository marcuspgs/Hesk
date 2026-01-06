## Instruction for usage & updating of theme files (Customer side only - Admin CSS has NOT yet been reworked)

## Theme files structure
```
| dist/ # contains production ready bundled & minified files
 (created via Adminpanel->Settings->Misc->Rebuild production assets)
    app.css
    app.min.css
    ... (.js currently not bundled here, to be done in the future)
    
| css/ # contains css files
    |__ core/   # contains non-bundled core files, that should *generally* only be used by Hesk staff, 
                    # so that they are  easy to update in future versions
                # Note: files are prefixed with x_xx_ naming format, 
                    # to ensure specific order of loading, to not break any dependencies
                    
                # General usage idea: To put any new CSS into the "most relevant" CSS file.
                # I.e. if you're generally adding a component (input, button, navigation...) 
                    # styling that should affect changes globally (in majority of places),
                    # you should put it in these relevant CSS files
                # If you you're editing/overwriting a bunch of misc CSS for elements on a specific page (i.e. tickets, or knowledge base)
                    # then you should put the CSS in those more "page-specific" files, like kb.css or tickets.css.
                # If you are unsure where to add some CSS, add it at the bottom of misc.css
                    
        default_theme_vars.css (contains all CSS variables that control the theme - i.e. reusage of colors that allows to easily be adjusted by users in their admin Look & Feel section)
        variables.css
        font_setup.css (loads up fonts...)
		common.css (correction classes, like margin, not displayed..)
		layout.css  (layout related things, like flex, justify, columns, wrappers..)
		layout_components.css (i.e. various wrappers, or holders for say forms or commonly used structures)	
		headers.css
		footer.css	
		buttons.css
		forms.css
		input.css
		icons.css
		dropdowns.css
		datepickers.css
		modal.css
		navigation.css
		tooltips.css
		responsive.css
		tickets.css
		kb.css
		pages.css // one file, with all page-specific CSS, like for admin ticket, i.e. knowledge base...  Ideally, they're all prefixed with the specific page_class, so it doesn't cause any conflicts
		popups.css	 // one file, that has specific popup styling with special popup styling etc.
		misc.css // leftover of any CSS that doesn't really fall into anything else
		deprecated.css // just css leftover, that would have to be triple-checked that deleting it would not cause any isseues	
        
        core_overrides.css // General empty file to allow people to overwrite core code, without having to make a custom theme for it?
        ...
		        
    |__themes/ # contains theme files (default ones, UI generated, or custom theme files pasted by users)
        |__ midnight_ocean.css
        |__ theme_example1.css
        
    theme_overrides.css # extra file to overwrite any theme files, that is not minimized specifically?
                    
                
```


### How are assets bundled in app.css?
1) First order from /core (in some predefined order, or via number ordering)
2) Any core_overrides.css (part of 1 really), that allows users to adjust core, without it being affected by future versions
3) NOTE ( I think) -> theme files are NOT part of app.css, but are overwritten above, as otherwise, there would need ot be a bundle created after editing a theme, which might be hassle for some
4) You can see how css files are loaded on customer side in theme/hesk3/customer/inc/header.inc.php
   - files in /core will be usable directly (without requiring rebundling), when hesk is in DEBUG MODE.
   - Outside of DEBUG MODE, if any changes were made, you should make sure to rebuild production assets (via Adminpanel->Settings->Misc->Rebuild production assets)

### How are assets loaded into a project?
First load any <head> scripts obviously
If dev, load all separately from core, then theme, then theme_overrides.css (ideally programatically, based on naming order?)
If live, load dist/app.css.min, then theme, then theme_overrides.css
Finally, laod any custom after <body> stuff as well, which obviously additionally overwrites anything
* Triple check for any custom loaded CSS/JS admin urls etc., as they are currently loaded - make sure it's backwards compatible, or at least very simple for users to upgrade/move things to the new version.
		
### USAGE/ADDITION OF CSS (PARTICULARLY COLORS!)
*EXCEPTION: For Admin CSS, you can just use css/app.css and insert new CSS at will (for now), as admin css has NOT yet been reworked to a more modular system.*

**!!! IMPORTANT: For Customer side adjustments, make sure to adhere to below instructions!!!**
- CSS Colors should NEVER be added directly into any new specific CSS that is added.
- Get familiar with the basics of usage of CSS variables, i.e. : https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_cascading_variables/Using_CSS_custom_properties
- In some situations, the usage of color-mix can be super handy, i.e. when you need a slightly different shade of a color (say of primary), that does not exist yet: https://developer.mozilla.org/en-US/docs/Web/CSS/color_value/color-mix
- NEVER add unnecessary new colors, unless absolutely necessary from a UX perspective. If at all possible, try to use (or derive) any new colors from the "MAIN theme colors" section in default_theme_vars.css
- *ALL* CSS Colors (borders, background, color, fill...) should:
  - only be defined (and added if needed-read below) in default_theme_vars.css
    - Always first look for reuse of existing theme colors - Get familiar with them in either in default_theme_vars.css, or in admin side under Settings -> Look & Feel
      - If a sensible color/variable already exists (i.e. you are adding a new HTML element, that should be colored in one of the primary Hesk colors, you should use var(--primary) in any CSS colors to refer to it.)
      - If you're adding some new component/element (i.e. not something that already widely exists, like an input, button...), and it can't sensibly use one of the existing color variables, you can then add that new color variable into default_theme_vars.css:
        - i.e. --my-new-element__bg: #somecolor; (where #somecolor is reading an existing variable color if possible, or if no sensible/matching color is defined yet, then add a new specific color here)
- Read the comments in default_theme_vars.css and variables.css for more specific on Hesk practices on color naming and usage.