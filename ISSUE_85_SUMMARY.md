# Issue #85 Investigation Summary

## Overview

Issue #85 requests adding an option to convert SVG text elements to vector paths to ensure consistent rendering across different platforms and eliminate font-related issues.

## Current State

The ui-avatars service currently generates SVGs with `<text>` elements that rely on system fonts. While this approach is:
- ✅ Fast and lightweight
- ✅ Simple to implement
- ✅ Uses system fonts for native appearance

It has limitations:
- ❌ Text rendering varies across platforms
- ❌ Missing fonts cause fallback behavior
- ❌ Font substitution can change appearance

## Investigation Results

### 1. Technical Feasibility

Text-to-path conversion in PHP is **technically possible** but requires:

**Dependencies:**
- Font files (.ttf or .otf) - Cannot use CSS font-family strings
- Font parsing library to read glyph shapes
- Path generation logic to convert glyphs to SVG paths

**Available Libraries:**
- `meyfa/php-svg` - Has TTF parser, supports text rendering
- `phenx/php-font-lib` - Can parse font files
- External tools: Inkscape CLI, librsvg

### 2. Implementation Complexity

**Medium to High Complexity**
- Estimated development time: 34-60 hours
- Requires bundling font file(s) with the application
- Need to handle character mapping, positioning, kerning
- Performance optimization required (caching essential)

### 3. Trade-offs

| Aspect | Text Element | Path Conversion |
|--------|-------------|-----------------|
| Rendering consistency | Variable | Guaranteed |
| Performance | Instant | 100-500ms overhead |
| File size | Smaller | Larger |
| Font flexibility | System fonts | Bundled fonts only |
| Maintenance | Simple | Complex |
| Browser compatibility | Excellent | Excellent |

## Recommendations

### For Maintainer

**Option A: Implement as opt-in feature** (Recommended)
- Add `text-to-path=1` query parameter
- Bundle one high-quality open-source font (Inter, Roboto, or Open Sans)
- Implement using `meyfa/php-svg` library
- Add aggressive caching for path-converted SVGs
- Document limitations clearly

**Option B: Provide workaround documentation**
- Document client-side conversion approach
- Provide Inkscape command-line examples
- Link to JavaScript libraries for post-processing
- Lower maintenance burden

**Option C: Decline request**
- Complexity outweighs benefits
- PNG format already solves the consistency problem
- Focus resources on other features

### For Users (Immediate Workarounds)

**1. Use PNG format instead:**
```
https://ui-avatars.com/api/?name=John+Doe&format=png
```

**2. Post-process SVGs with Inkscape:**
```bash
curl "https://ui-avatars.com/api/?name=John+Doe&format=svg" | \
  inkscape --pipe --export-text-to-path --export-plain-svg \
  --export-filename=output.svg
```

**3. Embed fonts in your application:**
```css
@font-face {
  font-family: 'Roboto';
  src: url('roboto.woff2') format('woff2');
}
```

**4. Use JavaScript for client-side conversion:**
```javascript
// Load SVG, convert text to path using opentype.js or similar
```

## Files Created

This investigation includes:

1. **ISSUE_85_ANALYSIS.md** - Detailed technical analysis
2. **ISSUE_85_POC.php** - Proof-of-concept implementation
3. **ISSUE_85_SUMMARY.md** - This summary document

## Next Steps

**If proceeding with implementation:**

1. ✅ Get maintainer approval
2. ⬜ Choose and license appropriate font file
3. ⬜ Select implementation approach (library vs CLI)
4. ⬜ Create proof-of-concept with real font
5. ⬜ Performance testing and optimization
6. ⬜ Update Input.php to add text-to-path parameter
7. ⬜ Modify api/index.php for path generation
8. ⬜ Add tests
9. ⬜ Update documentation
10. ⬜ Update cache key generation to include text-to-path flag

**If declining:**

1. ✅ Document workarounds in README or FAQ
2. ⬜ Close issue with explanation
3. ⬜ Link to this investigation

## Estimated Impact

**If implemented:**
- **Benefits:** Solves consistency issues for users who need it
- **Costs:** Increased complexity, maintenance burden, performance overhead
- **Risk:** Medium - adds complexity to simple, proven system

**Demand assessment needed:**
- How many users have this specific requirement?
- Are they in specialized use cases (embeds, PDFs, etc.)?
- Would PNG format solve their problem equally well?

## Conclusion

The feature is implementable but adds significant complexity to a currently simple and fast service. The decision should be based on:

1. **User demand** - Is this a common request?
2. **Strategic value** - Does it align with project goals?
3. **Resource availability** - Is 40-60 hours of dev time available?
4. **Maintenance commitment** - Can the added complexity be sustained?

The PNG format already provides consistent, font-independent rendering. Users with specific needs for path-based SVGs can use post-processing tools. A full implementation should only proceed if there's substantial demand from the user base.

---

**Investigation completed:** 2025-11-13
**Conducted by:** Claude Code
**Status:** Awaiting maintainer decision
