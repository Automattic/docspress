import fs from "node:fs/promises";
import path from "node:path";
import { describe, expect, it } from "vitest";

const root = process.cwd();
const blocksRoot = path.join(root, "plugins", "docspress-blocks", "blocks");
const completeTypographyKeys = [
  "fontFamily",
  "fontSize",
  "fontStyle",
  "fontWeight",
  "letterSpacing",
  "lineHeight",
  "textAlign",
  "textColumns",
  "textDecoration",
  "textIndent",
  "textTransform",
  "writingMode",
];
const headingTypographyKeys = completeTypographyKeys.filter((key) => key !== "fontSize");
const inheritedHeadingLevelKeys = completeTypographyKeys.filter(
  (key) => !["fontSize", "letterSpacing", "lineHeight"].includes(key)
);
const semanticHeadingScale = {
  h1: "heading-2",
  h2: "heading-3",
  h3: "heading-4",
  h4: "lead",
  h5: "body",
  h6: "small",
};
const styledElements = [
  "button",
  "caption",
  "heading",
  "h1",
  "h2",
  "h3",
  "h4",
  "h5",
  "h6",
  "link",
];
const expectCompleteThemePreset = (preset) => {
  expect(preset.settings.typography.fontSizes.map(({ slug }) => slug)).toEqual([
    "small",
    "body",
    "lead",
    "heading-4",
    "heading-3",
    "heading-2",
    "display",
  ]);
  expect(Object.keys(preset.styles).sort()).toEqual([
    "blocks",
    "color",
    "elements",
    "spacing",
    "typography",
  ]);
  expect(Object.keys(preset.styles.typography).sort()).toEqual(
    [...completeTypographyKeys].sort()
  );
  expect(preset.styles.typography.fontSize).toBe(
    "var(--wp--custom--content-font-size)"
  );
  expect(Object.keys(preset.styles.elements).sort()).toEqual([...styledElements].sort());
  expect(Object.keys(preset.styles.elements.button.typography).sort()).toEqual(
    [...completeTypographyKeys].sort()
  );
  expect(Object.keys(preset.styles.elements.caption.typography).sort()).toEqual(
    [...completeTypographyKeys].sort()
  );
  expect(Object.keys(preset.styles.elements.heading.typography).sort()).toEqual(
    [...headingTypographyKeys].sort()
  );
  expect(Object.keys(preset.styles.elements.link.typography).sort()).toEqual(
    [...completeTypographyKeys].sort()
  );

  for (const level of ["h1", "h2", "h3", "h4", "h5", "h6"]) {
    expect(Object.keys(preset.styles.elements[level].typography).sort()).toEqual(
      [...completeTypographyKeys].sort()
    );
    expect(preset.styles.elements[level].color).toEqual({
      background: null,
      text: null,
    });
    expect(preset.styles.elements[level].typography.fontSize).toBe(
      `var:preset|font-size|${semanticHeadingScale[level]}`
    );
    for (const key of inheritedHeadingLevelKeys) {
      expect(preset.styles.elements[level].typography[key]).toBeNull();
    }
  }

  expect(Object.keys(preset.styles.blocks).sort()).toEqual([
    "core/button",
    "core/code",
    "core/quote",
  ]);
  expect(preset.styles.blocks["core/code"].typography).toBeTruthy();
};
const expectCompleteColorPreset = (preset) => {
  expect(Object.keys(preset.styles).sort()).toEqual(["blocks", "color", "elements"]);
  expect(preset.styles.color).toEqual({
    background: "var:preset|color|paper",
    text: "var:preset|color|copy",
  });
  expect(Object.keys(preset.styles.elements).sort()).toEqual([...styledElements].sort());

  for (const element of ["caption", "heading", "link"]) {
    expect(preset.styles.elements[element].color).toEqual({
      background: "transparent",
      text: expect.any(String),
    });
  }
  for (const level of ["h1", "h2", "h3", "h4", "h5", "h6"]) {
    expect(preset.styles.elements[level].color).toEqual({
      background: null,
      text: null,
    });
  }

  expect(preset.styles.elements.button.color).toEqual({
    background: "var:preset|color|accent",
    text: "var:preset|color|paper",
  });
  expect(Object.keys(preset.styles.blocks).sort()).toEqual([
    "core/button",
    "core/code",
    "core/quote",
  ]);
};
const blockNames = [
  "api-request",
  "audience-paths",
  "callout",
  "code-tabs",
  "colorful-code",
  "file-tree",
  "flow",
  "hero",
  "prompt",
  "result",
  "terminal-session"
];
const legacyCustomizerSettings = [
  "custom_logo",
  "docspress_design_preset",
  "docspress_docs_root",
  "docspress_sidebar_source",
  "docspress_sidebar_menu",
  "docspress_sidebar_sort",
  "docspress_sidebar_show_root",
  "docspress_sidebar_depth",
  "docspress_sidebar_title",
  "docspress_show_sidebar_search",
  "docspress_search_placeholder",
  "docspress_show_version_selector",
  "docspress_homepage_layout",
  "docspress_homepage_show_latest_posts",
  "docspress_homepage_posts_title",
  "docspress_homepage_posts_count",
  "docspress_header_menu",
  "docspress_show_brand_suffix",
  "docspress_brand_suffix",
  "docspress_show_header_search",
  "docspress_header_search_label",
  "docspress_search_dialog_placeholder",
  "docspress_search_suggested_label",
  "docspress_search_no_results_label",
  "docspress_search_results_limit",
  "docspress_search_width",
  "docspress_search_height",
  "docspress_search_radius_mode",
  "docspress_search_radius",
  "docspress_search_overlay_opacity",
  "docspress_search_overlay_blur",
  "docspress_search_show_paths",
  "docspress_search_show_excerpts",
  "docspress_search_show_hints",
  "docspress_show_color_toggle",
  "docspress_default_color_mode",
  "docspress_show_repository",
  "docspress_github_url",
  "docspress_article_width",
  "docspress_sidebar_width",
  "docspress_toc_width",
  "docspress_border_radius",
  "docspress_content_density",
  "docspress_show_toc",
  "docspress_show_breadcrumbs",
  "docspress_show_pagination",
  "docspress_show_post_meta",
  "docspress_show_post_date",
  "docspress_show_post_author",
  "docspress_show_featured_images",
  "docspress_show_post_categories",
  "docspress_show_post_tags",
  "docspress_comments_on_pages",
  "docspress_comments_on_posts",
  "docspress_show_comment_count",
  "docspress_show_comment_avatars",
  "docspress_comment_avatar_size",
  "docspress_show_comment_dates",
  "docspress_discussion_title",
  "docspress_comment_form_title",
  "docspress_comments_closed_message",
  "docspress_show_edit_link",
  "docspress_wordpress_edit_label",
  "docspress_show_github_edit_link",
  "docspress_github_edit_label",
  "docspress_github_edit_repository_url",
  "docspress_github_edit_ref",
  "docspress_show_summary",
  "docspress_ui_font",
  "docspress_content_font",
  "docspress_heading_font",
  "docspress_content_font_size",
  "docspress_heading_weight",
  "docspress_show_kicker",
  "docspress_kicker_label",
  "docspress_toc_title",
  "docspress_show_footer",
  "docspress_footer_text",
  "docspress_footer_link_label",
  "docspress_footer_link_url"
];

describe("DocsPress block theme constraints", () => {
  it("provides a standard one-link Playground documentation experience", async () => {
    const docsBlueprintPath = path.join(root, "theme", "blueprint-docs.json");
    const docsBlueprint = JSON.parse(await fs.readFile(docsBlueprintPath, "utf8"));
    const localDocsBlueprint = JSON.parse(
      await fs.readFile(path.join(root, "theme", "blueprint-local-docs.json"), "utf8")
    );
    const generated = JSON.parse(
      await fs.readFile(path.join(root, "theme", "playground", "generated-docs.json"), "utf8")
    );
    const readme = await fs.readFile(path.join(root, "README.md"), "utf8");
    const setup = await fs.readFile(
      path.join(root, "theme", "playground", "setup.php"),
      "utf8"
    );
    const localDocsImporter = await fs.readFile(
      path.join(root, "theme", "playground", "import-local-docs.php"),
      "utf8"
    );

    expect(docsBlueprint.$schema).toBe(
      "https://playground.wordpress.net/blueprint-schema.json"
    );
    expect(docsBlueprint.landingPage).toBe("/docs/");
    expect(docsBlueprint.login).toBe(true);
    expect(docsBlueprint.steps).toEqual(
      expect.arrayContaining([
        expect.objectContaining({
          step: "installPlugin",
          pluginData: expect.objectContaining({
            url: "https://github.com/Automattic/docspress",
            path: "plugins/docspress-blocks",
          }),
        }),
        expect.objectContaining({
          step: "installTheme",
          themeData: expect.objectContaining({
            url: "https://github.com/Automattic/docspress",
            path: "theme",
          }),
        }),
        expect.objectContaining({
          step: "runPHP",
          code: expect.stringContaining("/docspress/playground/setup.php"),
        }),
      ])
    );
    expect(generated.generatedBy).toBe("scripts/build-playground-docs.mjs");
    expect(generated.pages.length).toBeGreaterThan(20);
    expect(generated.pages.some((page) => page.key === "docs")).toBe(true);
    expect(setup).toContain("'permalink_structure', '/%postname%/'");
    expect(readme).toContain(
      "https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FAutomattic%2Fdocspress%2Fmain%2Ftheme%2Fblueprint-docs.json"
    );
    expect(localDocsBlueprint.landingPage).toBe("/docs/");
    expect(localDocsBlueprint.login).toBe(true);
    expect(localDocsBlueprint.steps).toEqual(
      expect.arrayContaining([
        expect.objectContaining({
          step: "runPHP",
          code: expect.stringContaining("/playground/import-local-docs.php"),
        }),
      ])
    );
    expect(localDocsImporter).toContain("DOCSPRESS_LOCAL_DOCS_SOURCE");
    expect(localDocsImporter).toContain("docspress_local_docs_markdown_to_blocks");
    expect(localDocsImporter).toContain("_docspress_source_path");
    expect(readme).toContain("npx @wp-playground/cli@latest start");
    expect(readme).toContain("--mount=\"$PWD/docs:/wordpress/docspress-source-docs\"");
    expect(readme).toContain("--reset");
    expect(readme).not.toContain("npx docspress@latest playground");
    expect(readme).toContain("One command from docs to WordPress");
  });

  it("prefers the current mounted theme when reseeding the local Playground", async () => {
    const blueprint = JSON.parse(
      await fs.readFile(path.join(root, "theme", "blueprint.json"), "utf8")
    );
    const setupStep = blueprint.steps.find((step) => step.step === "runPHP");

    expect(setupStep.code).toContain("$slug = 'theme'");
    expect(setupStep.code).toContain("wp_get_theme( $slug )");
    expect(setupStep.code).toContain("require get_theme_root( $slug )");
  });

  it("uses the exact theme radius instead of independent minimums or pills", async () => {
    const stylePaths = [
      path.join(root, "plugins", "docspress-blocks", "assets", "code.css"),
      ...blockNames.map((name) => path.join(blocksRoot, name, "style.css"))
    ];
    const styles = (await Promise.all(stylePaths.map((file) => fs.readFile(file, "utf8")))).join("\n");

    expect(styles).not.toMatch(/border-radius:\s*(?:max\(|calc\(|999px)/);
    expect(styles).toContain("border-radius: var(--dp-radius, 10px);");
  });

  it("gives every companion block native Site Editor design controls", async () => {
    const editors = await Promise.all(
      blockNames.map((name) => fs.readFile(path.join(blocksRoot, name, "editor.js"), "utf8"))
    );

    for (const editor of editors) {
      expect(editor).toContain("themeStyle");
      expect(editor).toContain("designSupports");
    }
  });

  it("ships an editable Flow and native collapsible File Tree folders", async () => {
    const plugin = await fs.readFile(
      path.join(root, "plugins", "docspress-blocks", "docspress-blocks.php"),
      "utf8"
    );
    const flowEditor = await fs.readFile(path.join(blocksRoot, "flow", "editor.js"), "utf8");
    const flowRender = await fs.readFile(path.join(blocksRoot, "flow", "block.php"), "utf8");
    const flowStyles = await fs.readFile(path.join(blocksRoot, "flow", "style.css"), "utf8");
    const fileTreeEditor = await fs.readFile(path.join(blocksRoot, "file-tree", "editor.js"), "utf8");
    const fileTreeRender = await fs.readFile(path.join(blocksRoot, "file-tree", "block.php"), "utf8");
    const fileTreeStyles = await fs.readFile(path.join(blocksRoot, "file-tree", "style.css"), "utf8");

    expect(plugin).toContain("blocks/flow/block.php");
    expect(flowEditor).toContain("Starting number");
    expect(flowEditor).toContain("Add step");
    expect(flowEditor).toContain("Move up");
    expect(flowRender).toContain("'docspress/flow'");
    expect(flowRender).toContain("docspress-flow__marker");
    expect(flowStyles).toContain(".wp-block-docspress-flow .docspress-flow__title {");
    expect(flowStyles).not.toContain(".docspress-flow :where(.docspress-flow__title)");
    expect(fileTreeEditor).toContain("Allow readers to collapse folders");
    expect(fileTreeEditor).toContain("Expand folders by default");
    expect(fileTreeRender).toContain("<details");
    expect(fileTreeRender).toContain("<summary");
    expect(fileTreeRender).not.toContain('role="tree"');
    expect(fileTreeStyles).toContain(
      ".wp-block-docspress-file-tree .docspress-file-tree__entries"
    );
    expect(fileTreeStyles).toContain(
      ".wp-block-docspress-file-tree .docspress-file-tree__item"
    );
    expect(fileTreeStyles).toContain("min-height: 27px;");
  });

  it("is a block theme with editable templates and template parts", async () => {
    const theme = JSON.parse(await fs.readFile(path.join(root, "theme", "theme.json"), "utf8"));
    const templates = await fs.readdir(path.join(root, "theme", "templates"));
    const parts = await fs.readdir(path.join(root, "theme", "parts"));

    expect(theme.version).toBe(3);
    expect(templates).toContain("index.html");
    expect(templates).toContain("page.html");
    expect(templates).toContain("front-page.html");
    expect(parts).toEqual(expect.arrayContaining(["header.html", "footer.html", "comments.html"]));
    expect(theme.templateParts.map((part) => part.name)).toEqual(
      expect.arrayContaining(["header", "footer", "comments"])
    );
    expect(theme.templateParts.find((part) => part.name === "comments")?.area).toBe("comments");
  });

  it("gives every template-part block a meaningful editor label", async () => {
    const templates = await fs.readdir(path.join(root, "theme", "templates"));
    const styles = await fs.readFile(path.join(root, "theme", "style.css"), "utf8");
    const expectedNames = {
      comments: "Comments",
      footer: "Footer",
      header: "Header"
    };

    for (const filename of templates.filter((name) => name.endsWith(".html"))) {
      const markup = await fs.readFile(path.join(root, "theme", "templates", filename), "utf8");
      const references = [
        ...markup.matchAll(/<!-- wp:template-part (\{[^\n]+\}) \/-->/g)
      ].map((match) => JSON.parse(match[1]));

      for (const attributes of references) {
        expect(attributes.metadata?.name).toBe(expectedNames[attributes.slug]);
        if (attributes.slug === "header") {
          expect(attributes.align).toBe("full");
        }
      }
    }

    expect(styles).toContain(".site-header.wp-block-group");
    expect(styles).toContain("max-width: none");
  });

  it("composes standalone Pages and discussion as editable native blocks", async () => {
    const noSidebar = await fs.readFile(
      path.join(root, "theme", "templates", "page-no-sidebar.html"),
      "utf8"
    );
    const wide = await fs.readFile(
      path.join(root, "theme", "templates", "page-wide.html"),
      "utf8"
    );
    const comments = await fs.readFile(
      path.join(root, "theme", "parts", "comments.html"),
      "utf8"
    );
    const styles = await fs.readFile(path.join(root, "theme", "style.css"), "utf8");

    for (const template of [noSidebar, wide]) {
      expect(template).toContain('"tagName":"header","className":"entry-header"');
      expect(template).toContain('<p class="entry-kicker">Page</p>');
      expect(template).toContain('"className":"entry-title"');
      expect(template).toContain("wp:docspress/page-summary");
      expect(template).toContain("wp:post-featured-image");
      expect(template).toContain("wp:post-content");
      expect(template).toContain("wp:docspress/edit-links");
      expect(template).toContain('"slug":"comments"');
    }

    expect(comments).toContain('"tagName":"header","className":"comments-header"');
    expect(comments).toContain("comments-intro");
    expect(comments).toContain('"className":"comment-list"');
    expect(comments).toContain('"className":"comment-actions"');
    expect(comments).toContain("wp:comment-edit-link");
    expect(comments).toContain('"className":"comment-form-shell"');
    expect(styles).toContain(".comments-area .comments-header.wp-block-group");
    expect(styles).not.toContain(".comments-area .comment-body::before");
    expect(styles).toContain(".comments-area .comment-form-shell");
    expect(styles).toMatch(
      /\.comments-area \.comment-body\s*\{[^}]*border:\s*0;[^}]*box-shadow:\s*none;/s
    );
    expect(styles).toMatch(
      /\.comments-area \.comment-form-shell\s*\{[^}]*border:\s*0;[^}]*box-shadow:\s*none;/s
    );
  });

  it("composes the 404 recovery page from editable theme-native blocks", async () => {
    const template = await fs.readFile(
      path.join(root, "theme", "templates", "404.html"),
      "utf8"
    );
    const styles = await fs.readFile(path.join(root, "theme", "style.css"), "utf8");

    expect(template).toContain('"tagName":"header","className":"entry-header error-header"');
    expect(template).toContain('<p class="entry-kicker">Error 404</p>');
    expect(template).toContain('"className":"entry-title error-title"');
    expect(template).toContain('"className":"entry-summary error-summary"');
    expect(template).toContain('"className":"error-search"');
    expect(template).toContain('"className":"error-actions"');
    expect(template).not.toContain('"className":"error-code-card"');
    expect(template).not.toContain('<p class="error-code">404</p>');
    expect(template).toContain('"slug":"header"');
    expect(template).toContain('"slug":"footer"');
    expect(styles).toContain(".error-shell.wp-block-group");
    expect(styles).toContain(".error-recovery.wp-block-group");
    expect(styles).not.toContain(".error-code-card");
    expect(styles).toMatch(
      /\.error-recovery\.wp-block-group\s*\{[^}]*border:\s*0;[^}]*box-shadow:\s*none;/s
    );
  });

  it("composes Blog Home from the same editable entry and card system", async () => {
    const template = await fs.readFile(
      path.join(root, "theme", "templates", "home.html"),
      "utf8"
    );
    const styles = await fs.readFile(path.join(root, "theme", "style.css"), "utf8");

    expect(template).toContain('"className":"site-main archive-main blog-main"');
    expect(template).toContain('"tagName":"header","className":"archive-heading blog-heading"');
    expect(template).toContain('<p class="entry-kicker">Blog</p>');
    expect(template).toContain('<h1 class="wp-block-heading entry-title">Updates</h1>');
    expect(template).toContain('"className":"entry-summary"');
    expect(template).toContain('"className":"archive-card-grid is-style-doc-cards"');
    expect(template).toContain('"className":"result-card content-card archive-card"');
    expect(template).toContain("wp:post-terms");
    expect(template).toContain("wp:post-date");
    expect(template).toContain("wp:read-more");
    expect(template).toContain('"className":"archive-pagination"');
    expect(template).not.toContain('"type":"archive"');
    expect(template).not.toContain("Archive title");
    expect(styles).toContain("margin-left: 0 !important;");
  });

  it("gives Index a complete editable fallback instead of an empty inherited query", async () => {
    const template = await fs.readFile(
      path.join(root, "theme", "templates", "index.html"),
      "utf8"
    );

    expect(template).toContain(
      '"className":"site-main archive-main blog-main index-main"'
    );
    expect(template).toContain(
      '"tagName":"header","className":"archive-heading blog-heading index-heading"'
    );
    expect(template).toContain('<p class="entry-kicker">Blog</p>');
    expect(template).toContain('<h1 class="wp-block-heading entry-title">Updates</h1>');
    expect(template).toContain('"postType":"post"');
    expect(template).toContain('"perPage":10');
    expect(template).toContain('"inherit":true');
    expect(template).toContain('"className":"archive-card-grid is-style-doc-cards"');
    expect(template).toContain('"className":"result-card content-card archive-card"');
    expect(template).toContain('"className":"archive-pagination"');
    expect(template).toContain("wp:query-no-results");
    expect(template).not.toContain('"query":{"inherit":true}');
  });

  it("gives the generic Archive editor a concrete fallback query", async () => {
    const template = await fs.readFile(
      path.join(root, "theme", "templates", "archive.html"),
      "utf8"
    );

    expect(template).toContain('"postType":"post"');
    expect(template).toContain('"perPage":10');
    expect(template).toContain('"orderBy":"date"');
    expect(template).toContain('"inherit":true');
    expect(template).not.toContain('"query":{"inherit":true}');
  });

  it("centers empty-state headings despite global heading alignment", async () => {
    for (const templateName of ["archive", "front-page", "home", "index"]) {
      const template = await fs.readFile(
        path.join(root, "theme", "templates", `${templateName}.html`),
        "utf8"
      );

      expect(template).toContain('wp:heading {"textAlign":"center","level":3}');
      expect(template).toContain(
        '<h3 class="wp-block-heading has-text-align-center">'
      );
    }
  });

  it("groups brand palettes under three native global style families", async () => {
    const functions = await fs.readFile(path.join(root, "theme", "functions.php"), "utf8");
    const families = {
      "wordpress-org": {
        headingWeight: "700",
        kicker: {
          border: "0",
          markerWidth: "18px",
          shadow: "none"
        },
        radius: "2px",
        ruleWidth: "54px",
        title: "WordPress.org",
        titleSize: "clamp(42px, 5.4vw, 64px)",
        variants: ["blueberry", "lemon", "purple"]
      },
      "wordpress-com": {
        headingWeight: "700",
        kicker: {
          border: "0",
          markerWidth: "0",
          shadow: "inset 0 -2px 0 var(--dp-blue)"
        },
        radius: "4px",
        ruleWidth: "48px",
        title: "WordPress.com",
        titleSize: "clamp(44px, 5.7vw, 68px)",
        variants: ["blue", "ink", "warm"]
      },
      jetpack: {
        headingWeight: "700",
        kicker: {
          border: "0",
          markerWidth: "5px",
          shadow: "none"
        },
        radius: "4px",
        ruleWidth: "100%",
        title: "Jetpack",
        titleSize: "clamp(42px, 5.5vw, 66px)",
        variants: ["green", "electric", "forest"]
      }
    };

    expect(functions).toContain("styles/theme/*.json");
    expect(functions).toContain("styles/color/*/*.json");
    expect(functions).toContain("styles/block/*.json");

    for (const [family, config] of Object.entries(families)) {
      const familyVariation = JSON.parse(
        await fs.readFile(
          path.join(root, "theme", "styles", "theme", `${family}.json`),
          "utf8"
        )
      );

      expect(familyVariation.version).toBe(3);
      expect(familyVariation.title).toBe(config.title);
      expect(familyVariation.settings.custom.radius).toBe(config.radius);
      expect(familyVariation.settings.custom.entryKickerBorder).toBe(config.kicker.border);
      expect(familyVariation.settings.custom.entryKickerMarkerWidth).toBe(
        config.kicker.markerWidth
      );
      expect(familyVariation.settings.custom.entryKickerShadow).toBe(config.kicker.shadow);
      expect(familyVariation.settings.custom.entryTitleSize).toBe(config.titleSize);
      expect(familyVariation.settings.custom.entryRuleWidth).toBe(config.ruleWidth);
      expect(familyVariation.settings.custom.headingWeight).toBe(config.headingWeight);
      expect(familyVariation.settings.custom.entryTitleWeight).toBe(config.headingWeight);
      expect(familyVariation.styles.elements.heading.typography.fontWeight).toBe(
        config.headingWeight
      );
      expect(familyVariation.settings.color.palette).toHaveLength(24);
      expectCompleteThemePreset(familyVariation);

      for (const variant of config.variants) {
        const colorVariation = JSON.parse(
          await fs.readFile(
            path.join(root, "theme", "styles", "color", family, `${variant}.json`),
            "utf8"
          )
        );

        expect(colorVariation.version).toBe(3);
        expect(colorVariation.settings).toEqual({
          color: expect.objectContaining({ palette: expect.any(Array) })
        });
        expect(colorVariation.settings.color.palette).toHaveLength(24);
        expect(colorVariation.settings.color.palette.map(({ slug }) => slug)).toEqual(
          expect.arrayContaining(["highlight-strong", "dark-accent", "dark-code"])
        );
        expectCompleteColorPreset(colorVariation);
      }
    }
  });

  it("preserves the original DocsPress design as the block theme default", async () => {
    const theme = JSON.parse(await fs.readFile(path.join(root, "theme", "theme.json"), "utf8"));
    const styles = await fs.readFile(path.join(root, "theme", "style.css"), "utf8");
    const functions = await fs.readFile(path.join(root, "theme", "functions.php"), "utf8");
    const header = await fs.readFile(path.join(root, "theme", "parts", "header.html"), "utf8");
    const defaultLogo = await fs.readFile(
      path.join(root, "theme", "assets", "images", "docspress-hybrid-logo.png")
    );
    const frontPage = await fs.readFile(
      path.join(root, "theme", "templates", "front-page.html"),
      "utf8"
    );
    const pageTemplate = await fs.readFile(
      path.join(root, "theme", "templates", "page.html"),
      "utf8"
    );
    const palette = Object.fromEntries(
      theme.settings.color.palette.map(({ slug, color }) => [slug, color])
    );

    expect(palette).toMatchObject({
      accent: "#005cb3",
      "accent-strong": "#004a91",
      highlight: "#fec408",
      "highlight-strong": "#fe8301",
      "dark-accent": "#62b5ff",
      ink: "#232323"
    });
    expect(header.match(/<!-- wp:site-logo/g)).toHaveLength(1);
    expect(header).toContain('"shouldSyncIcon":true');
    expect(header).not.toContain("wp:site-title");
    expect(header).toContain('wp:paragraph {"className":"brand-title"}');
    expect(header).toContain('<p class="brand-title"><a href="/">DocsPress</a></p>');
    expect(defaultLogo.byteLength).toBeGreaterThan(1000);
    expect(functions).toContain("function docspress_maybe_seed_default_site_logo()");
    expect(functions).toContain("update_option( 'site_logo', $logo_id );");
    expect(functions).toContain("update_option( 'site_icon', $logo_id );");
    expect(styles).not.toContain(".brand::before");
    expect(styles).toContain(".brand-mark img {");
    expect(styles).toMatch(
      /\.brand-title\s*\{[^}]*font-weight:\s*var\(--wp--custom--heading-weight,\s*700\);[^}]*\}/s
    );
    expect(styles).toContain("background: var(--dp-highlight);");
    expect(theme.settings.custom.entryKickerRadius).toBe("999px");
    expect(theme.settings.custom.entryKickerShadow).toBe(
      "3px 3px 0 var(--dp-highlight-strong)"
    );
    expect(theme.settings.custom.entryTitleSize).toBe("clamp(42px, 5.6vw, 68px)");
    expect(theme.settings.custom.sidebarWidth).toBe("266px");
    expect(theme.settings.custom.tocWidth).toBe("226px");
    expect(theme.settings.custom.contentWidth).toBe("770px");
    expect(theme.settings.layout.contentSize).toBe("770px");
    expect(theme.settings.layout.wideSize).toBe("1100px");
    expect(styles).toContain("var(--wp--custom--entry-kicker-border");
    expect(styles).toContain("var(--wp--custom--entry-title-size");
    expect(styles).toContain("var(--wp--custom--entry-rule-background");
    expect(styles).toContain("var(--wp--preset--color--dark-accent, #62b5ff)");
    expect(header).toContain('"label":"Docs"');
    expect(header).toContain('"label":"Kitchen Sink"');
    expect(header).toContain('"label":"GitHub"');
    expect(header).toContain('"width":34');
    expect(header).not.toContain('"blockGap"');
    expect(header).not.toContain('"justifyContent":"space-between"');
    expect(header).not.toContain('"className":"brand-wordpress","fontSize":"small"');
    expect(styles).toContain(".wp-site-blocks > header.wp-block-template-part {");
    expect(styles).toContain("margin: 0 0 0 auto;");
    expect(styles).toContain(".primary-navigation a.is-current-page");
    expect(styles).toMatch(
      /\.primary-navigation a\.is-current-page,[\s\S]*?background:\s*var\(--dp-highlight\);[\s\S]*?color:\s*var\(--dp-ink\);/
    );
    expect(styles).toMatch(
      /\.primary-navigation \.wp-block-navigation__container\s*\{[^}]*flex-wrap:\s*nowrap;[^}]*gap:\s*4px !important;/s
    );
    expect(styles).toMatch(
      /@media \(max-width: 1024px\)\s*\{[\s\S]*?\.header-inner > \.primary-navigation,[\s\S]*?\.search-shortcut span,[\s\S]*?\.search-shortcut kbd[\s\S]*?display:\s*none;[\s\S]*?\.menu-toggle[\s\S]*?display:\s*inline-flex;[\s\S]*?\}/
    );
    expect(styles).toMatch(
      /\.brand\.wp-block-group\s*\{[^}]*gap:\s*11px;[^}]*\}/s
    );
    expect(styles).toMatch(
      /\.header-actions\.wp-block-group\s*\{[^}]*gap:\s*8px;[^}]*\}/s
    );
    expect(styles).toMatch(
      /\.repository-link\.wp-block-social-links\s*\{[^}]*gap:\s*8px;[^}]*\}/s
    );
    expect(styles).toContain(".repository-link .wp-block-social-link-anchor {");
    expect(pageTemplate).toContain(
      '"tagName":"header","className":"entry-header","layout":{"type":"default"}'
    );
    expect(pageTemplate).toContain("wp:docspress/page-summary");
    expect(pageTemplate).not.toContain("wp:post-excerpt");
    expect(frontPage).toContain('className":"homepage-main"');
    expect(frontPage).toContain("Latest updates");
    expect(frontPage).toContain("wp:query");
    expect(frontPage).toContain("homepage-card-grid");
  });

  it("seeds the restrained fkadev.blog hero instead of an editorial treatment", async () => {
    const setup = await fs.readFile(
      path.join(root, "theme", "playground", "setup.php"),
      "utf8"
    );
    const heroSeed = setup.match(/\$hero_attributes = array\(([\s\S]*?)\n\);/)?.[1] || "";

    expect(heroSeed).toContain("'mediaUrl'");
    expect(heroSeed).not.toContain("'visualLabel'");
    expect(heroSeed).not.toContain("'layout'");
    expect(heroSeed).not.toContain("'tone'");
    expect(heroSeed).not.toContain("'showGrid'");
    expect(heroSeed).not.toContain("'showOrbit'");
  });

  it("ships reusable block style variants", async () => {
    const variants = ["soft-panel.json", "outline-card.json", "signal-band.json"];

    for (const filename of variants) {
      const variation = JSON.parse(
        await fs.readFile(path.join(root, "theme", "styles", "block", filename), "utf8")
      );
      expect(variation.blockTypes.length).toBeGreaterThanOrEqual(3);
      expect(variation.slug).toBeTruthy();
    }
  });

  it("exposes every documentation shell component in the block editor", async () => {
    const editor = await fs.readFile(
      path.join(root, "theme", "assets", "js", "block-components.js"),
      "utf8"
    );
    const php = await fs.readFile(path.join(root, "theme", "inc", "blocks.php"), "utf8");
    const styles = await fs.readFile(path.join(root, "theme", "style.css"), "utf8");
    const components = [
      "docs-navigation",
      "command-search",
      "breadcrumbs",
      "table-of-contents",
      "page-summary",
      "edit-links",
      "adjacent-navigation",
      "color-mode-toggle",
      "docs-menu-toggle"
    ];

    for (const component of components) {
      expect(editor).toContain(`registerComponent( '${component}'`);
      expect(php).toContain(`'${component}' => array(`);
    }
    expect(editor).toContain("designSupports");
    expect(editor).toContain("config.preview");
    expect(editor).toContain("config.EditorPreview");
    expect(editor).toContain("const { Disabled, PanelBody");
    expect(editor).toContain("CommandSearchEditorPreview");
    expect(editor).toContain("Preview search dialog");
    expect(editor).toContain("Click the search trigger or use the block toolbar");
    expect(editor).toContain("docspress-command-search-editor-overlay");
    expect(editor).toContain("controlGroup: 'content'");
    expect(editor).toContain("( ! config.controlGroup || isSelected )");
    expect(editor).toContain("updateComponentNavigatorOptions");
    expect(editor).toContain("docspress-quick-navigation-chevron");
    expect(editor).toContain("attributes.suggestedLabel");
    expect(styles).toContain(".docspress-command-search-editor-overlay {");
    expect(styles).toContain(".search-dialog.is-editor-preview {");
    expect(editor).toContain("Breadcrumbs preview");
    expect(editor).toContain("DocsPress documentation");
    expect(editor).not.toContain("Breadcrumbs appear on child Pages.");
    expect(editor).toContain("Table of contents preview");
    expect(editor).toContain("Install DocsPress");
    expect(editor).toContain("Configure publishing");
    expect(editor).not.toContain("Add headings to populate the table of contents.");
    expect(editor).toContain("Page actions preview");
    expect(editor).toContain("Enable a WordPress or GitHub action to preview it.");
    expect(editor).not.toContain("Edit actions appear on singular content.");
    expect(editor).toContain("A manually written Page excerpt appears here.");
    expect(editor).toContain("Previous and next Page preview");
    expect(editor).toContain("Default mode");
    expect(editor).toContain("Sidebar width");
    expect(editor).toContain("Column width");
    expect(editor).toContain("updateTemplatePartNavigatorLabels");
    expect(editor).toContain("block-editor-block-quick-navigation__item");
    expect(editor).toContain("Header");
    expect(editor).toContain("Comments");
    expect(editor).toContain("Footer");
    expect(php).toContain("docspress_render_page_summary");
    expect(php).toContain("'defaultMode'");
    expect(php).toContain("'width'             => array( 'type' => 'number', 'default' => 266 )");
    expect(php).toContain("'width'    => array( 'type' => 'number', 'default' => 226 )");
    expect(php).toContain("docspress_component_supports()");
  });

  it("lets Global Styles win for headings and content call-to-action buttons", async () => {
    const theme = JSON.parse(await fs.readFile(path.join(root, "theme", "theme.json"), "utf8"));
    const styles = await fs.readFile(path.join(root, "theme", "style.css"), "utf8");
    const components = await fs.readFile(
      path.join(root, "theme", "assets", "js", "block-components.js"),
      "utf8"
    );
    const functions = await fs.readFile(path.join(root, "theme", "functions.php"), "utf8");
    const php = await fs.readFile(path.join(root, "theme", "inc", "blocks.php"), "utf8");
    const heroEditor = await fs.readFile(
      path.join(blocksRoot, "hero", "editor.js"),
      "utf8"
    );
    const heroRender = await fs.readFile(
      path.join(blocksRoot, "hero", "block.php"),
      "utf8"
    );
    const heroStyles = await fs.readFile(
      path.join(blocksRoot, "hero", "style.css"),
      "utf8"
    );
    const audienceStyles = await fs.readFile(
      path.join(blocksRoot, "audience-paths", "style.css"),
      "utf8"
    );
    const audienceEditor = await fs.readFile(
      path.join(blocksRoot, "audience-paths", "editor.js"),
      "utf8"
    );
    const audienceRender = await fs.readFile(
      path.join(blocksRoot, "audience-paths", "block.php"),
      "utf8"
    );
    const resultStyles = await fs.readFile(
      path.join(blocksRoot, "result", "style.css"),
      "utf8"
    );
    const promptStyles = await fs.readFile(
      path.join(blocksRoot, "prompt", "style.css"),
      "utf8"
    );
    const calloutStyles = await fs.readFile(
      path.join(blocksRoot, "callout", "style.css"),
      "utf8"
    );
    const fileTreeStyles = await fs.readFile(
      path.join(blocksRoot, "file-tree", "style.css"),
      "utf8"
    );

    expect(styles).toContain(":where(.entry-title) {");
    expect(styles).toContain(".entry-content :where(h2)");
    expect(styles).toContain(":where(.entry-content) :where(.wp-block-button__link)");
    expect(styles).toContain(":where(.page-action) {");
    expect(styles).toContain(".page-action-github.wp-element-button {");
    expect(styles).toContain(".page-action-github.wp-element-button:hover {");
    expect(styles).not.toMatch(/^\.entry-title\s*\{/m);
    expect(styles).not.toMatch(/^\.entry-content h[2-4][,{ ]/m);
    expect(styles).not.toMatch(/^\.entry-content \.wp-block-button__link\s*\{/m);
    expect(theme.styles.color).toEqual({
      background: "var(--dp-paper)",
      text: "var(--dp-copy)",
    });
    expect(theme.styles.typography).toMatchObject({
      fontFamily: "var:preset|font-family|ui",
      fontSize: "var(--wp--custom--content-font-size)",
      lineHeight: "1.78",
    });
    expect(theme.styles.elements.heading.typography.fontWeight).toBe(
      "var(--wp--custom--heading-weight)"
    );
    expectCompleteThemePreset(theme);
    expect(theme.styles.elements.heading.color.text).toBe("var(--dp-ink)");
    expect(theme.styles.elements.link.color.text).toBe("var(--dp-blue-dark)");

    const bodyRule = styles.match(/\nbody\s*\{([^}]*)\}/)?.[1] ?? "";
    const linkRule = styles.match(/\na\s*\{([^}]*)\}/)?.[1] ?? "";
    const contentRule = styles.match(/\n\.entry-content\s*\{([^}]*)\}/)?.[1] ?? "";
    const summaryRule = styles.match(/\n\.entry-summary\s*\{([^}]*)\}/)?.[1] ?? "";
    const docsNavRule = styles.match(/\n\.docs-nav a\s*\{([^}]*)\}/)?.[1] ?? "";
    const cssRule = (selector) => {
      const escapedSelector = selector.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
      return styles.match(new RegExp(`\\n${escapedSelector}\\s*\\{([^}]*)\\}`, "m"))?.[1] ?? "";
    };

    for (const rule of [bodyRule, linkRule, contentRule, docsNavRule]) {
      expect(rule).not.toMatch(/(?:^|\s)color\s*:/);
    }
    expect(bodyRule).not.toMatch(/(?:^|\s)background\s*:/);
    for (const rule of [bodyRule, contentRule, summaryRule]) {
      expect(rule).not.toMatch(/(?:^|\s)font-family\s*:/);
      expect(rule).not.toMatch(/(?:^|\s)font-size\s*:/);
      expect(rule).not.toMatch(/(?:^|\s)line-height\s*:/);
    }
    for (const selector of [
      ".comments-eyebrow",
      ".comment-author",
      ".comment-metadata",
      ".comment-form label",
      ".comments-area .comments-eyebrow",
      ".comments-area .comments-intro",
      ".comments-area .wp-block-comment-author-name",
      ".comments-area .wp-block-comment-date",
      ".comments-area .wp-block-comment-content",
    ]) {
      const rule = cssRule(selector);
      expect(rule).not.toMatch(/(?:^|\s)color\s*:/);
      expect(rule).not.toMatch(/(?:^|\s)font-family\s*:/);
    }
    for (const selector of [".comments-area .comments-intro", ".comments-area .wp-block-comment-content"]) {
      const rule = cssRule(selector);
      expect(rule).not.toMatch(/(?:^|\s)font-size\s*:/);
      expect(rule).not.toMatch(/(?:^|\s)line-height\s*:/);
    }
    expect(styles).toMatch(
      /\.comment-form input\[type="text"\],[\s\S]*?\.post-password-form input\[type="password"\]\s*\{[^}]*color:\s*inherit;[^}]*font:\s*inherit;/
    );
    const commentSubmitRule = cssRule(
      `.comment-form .submit,
.post-password-form input[type="submit"]`
    );
    for (const property of ["background", "border", "border-radius", "color", "font-weight", "padding"]) {
      expect(commentSubmitRule).not.toMatch(new RegExp(`(?:^|\\s)${property}\\s*:`));
    }
    for (const rule of styles.matchAll(/:where\(\.entry-title\)\s*\{([^}]*)\}/g)) {
      expect(rule[1]).not.toContain("font-family:");
      expect(rule[1]).not.toMatch(/(?:^|\s)color\s*:/);
    }

    for (const [css, selector] of [
      [resultStyles, ".wp-block-docspress-result"],
      [promptStyles, ".wp-block-docspress-prompt"],
      [calloutStyles, ".docspress-callout"],
      [fileTreeStyles, ".wp-block-docspress-file-tree"],
    ]) {
      const escapedSelector = selector.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
      const rule = css.match(new RegExp(`^${escapedSelector}\\s*\\{([^}]*)\\}`, "m"))?.[1] ?? "";
      expect(rule).not.toMatch(/(?:^|\s)color\s*:/);
    }
    expect(php).toContain("page-action-wordpress wp-element-button");
    expect(php).toContain("page-action-github wp-element-button");
    expect(components).toContain("page-action ${ className } wp-element-button");
    expect(heroRender).toContain("docspress-hero__button--primary wp-element-button");
    expect(heroRender).toContain("docspress-hero__button--secondary wp-element-button");
    expect(heroEditor).toContain("docspress-hero__button--primary wp-element-button");
    expect(heroEditor).toContain("docspress-hero__button--secondary wp-element-button");
    expect(heroStyles).toContain(":where(.docspress-hero__title) {");
    expect(heroStyles).toContain(":where(.docspress-hero__button) {");
    expect(heroStyles).toContain(
      ".docspress-hero__button--secondary.wp-element-button {"
    );
    expect(audienceStyles).toContain(
      ":where(.docspress-audience-paths .docspress-audience-paths__title) {"
    );
    expect(audienceStyles).toContain(
      ".docspress-audience-paths--compact .docspress-audience-paths__title {"
    );
    expect(audienceStyles).toContain(
      ".docspress-audience-paths.has-text-color :is("
    );
    expect(audienceStyles).toContain("container-type: inline-size;");
    expect(audienceStyles).toContain("@container (max-width: 820px)");
    expect(audienceEditor).not.toContain("textColor:");
    expect(audienceRender).not.toContain("'textColor'");
    expect(functions).toContain(
      "function docspress_inherit_post_title_typography_from_headings"
    );
    expect(functions).toContain(
      "function docspress_migrate_legacy_post_title_typography"
    );
    expect(functions).toContain(
      "WP_Theme_JSON_Resolver::get_user_global_styles_post_id()"
    );
    expect(functions).toContain(
      "docspress_post_title_typography_migration"
    );
    expect(functions).toContain("wp_update_post(");
    expect(functions).toContain("'wp_theme_json_data_user'");
    expect(functions).toContain(
      "$data['styles']['blocks']['core/post-title']['typography']['fontFamily']"
    );
    expect(theme.styles.blocks?.["core/post-title"]).toBeUndefined();
    expect(theme.styles.blocks?.["core/post-content"]).toBeUndefined();
    expect(theme.styles.blocks?.["core/post-excerpt"]).toBeUndefined();

    for (const family of ["wordpress-org", "wordpress-com", "jetpack"]) {
      const variation = JSON.parse(
        await fs.readFile(
          path.join(root, "theme", "styles", "theme", `${family}.json`),
          "utf8"
        )
      );
      expect(variation.styles.blocks?.["core/post-title"]).toBeUndefined();
    }
  });

  it("lets Global Styles flow through the homepage shell and custom blocks", async () => {
    const theme = JSON.parse(await fs.readFile(path.join(root, "theme", "theme.json"), "utf8"));
    const styles = await fs.readFile(path.join(root, "theme", "style.css"), "utf8");
    const header = await fs.readFile(path.join(root, "theme", "parts", "header.html"), "utf8");
    const footer = await fs.readFile(path.join(root, "theme", "parts", "footer.html"), "utf8");
    const heroEditor = await fs.readFile(path.join(blocksRoot, "hero", "editor.js"), "utf8");
    const heroRender = await fs.readFile(path.join(blocksRoot, "hero", "block.php"), "utf8");
    const heroStyles = await fs.readFile(path.join(blocksRoot, "hero", "style.css"), "utf8");
    const audienceEditor = await fs.readFile(
      path.join(blocksRoot, "audience-paths", "editor.js"),
      "utf8"
    );
    const audienceRender = await fs.readFile(
      path.join(blocksRoot, "audience-paths", "block.php"),
      "utf8"
    );
    const audienceStyles = await fs.readFile(
      path.join(blocksRoot, "audience-paths", "style.css"),
      "utf8"
    );
    const cssRule = (css, selector) => {
      const escapedSelector = selector.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
      return css.match(new RegExp(`\\n${escapedSelector}\\s*\\{([^}]*)\\}`, "m"))?.[1] ?? "";
    };

    for (const selector of [
      ".site-header",
      ".site-footer",
      ".result-card",
      ".result-card p",
      ".content-card-thumbnail",
    ]) {
      expect(cssRule(styles, selector)).not.toMatch(/(?:^|\s)background\s*:/);
    }
    for (const selector of [
      ".brand-wordpress",
      ".site-footer",
      ".result-card p",
      ".entry-meta",
      ".content-card-taxonomy",
    ]) {
      expect(cssRule(styles, selector)).not.toMatch(/(?:^|\s)color\s*:/);
    }
    for (const selector of [
      ".brand",
      ".primary-navigation a",
      ".footer-navigation a",
      ".site-footer",
      ".result-card p",
    ]) {
      const rule = cssRule(styles, selector);
      expect(rule).not.toMatch(/(?:^|\s)font-family\s*:/);
      expect(rule).not.toMatch(/(?:^|\s)font-size\s*:/);
      expect(rule).not.toMatch(/(?:^|\s)font-weight\s*:/);
    }
    for (const selector of [
      ".entry-kicker",
      ".entry-meta",
      ".content-card-taxonomy",
      ".content-card .entry-meta",
      ".content-card-link",
    ]) {
      const rule = cssRule(styles, selector);
      expect(rule).not.toMatch(/(?:^|\s)font-family\s*:/);
      expect(rule).not.toMatch(/(?:^|\s)font-size\s*:/);
      expect(rule).not.toMatch(/(?:^|\s)font-weight\s*:/);
      expect(rule).not.toMatch(/(?:^|\s)letter-spacing\s*:/);
      expect(rule).not.toMatch(/(?:^|\s)text-transform\s*:/);
    }
    expect(styles).not.toMatch(
      /:where\(\.result-card\) :where\(h2\) a\s*\{[^}]*color\s*:/s
    );
    expect(styles).not.toMatch(
      /:where\(\.section-heading\) :where\(h2\)\s*\{[^}]*font-family\s*:/s
    );
    expect(cssRule(styles, ".entry-kicker")).not.toMatch(/(?:^|\s)color\s*:/);
    expect(styles).not.toContain(".site-footer a {");
    expect(footer).toContain("<!-- wp:paragraph -->");
    expect(footer).not.toContain('"textColor"');
    expect(footer).not.toContain('"fontSize"');
    expect(header).not.toContain('"iconColor"');
    expect(header).not.toContain("has-icon-color");
    expect(theme.styles.blocks?.["core/navigation"]).toBeUndefined();
    expect(theme.styles.blocks?.["core/site-title"]).toBeUndefined();

    for (const [css, selector] of [
      [heroStyles, ".docspress-hero"],
      [audienceStyles, ".docspress-audience-paths"],
    ]) {
      const rule = cssRule(css, selector);
      expect(rule).not.toMatch(/(?:^|\s)background\s*:/);
      expect(rule).not.toMatch(/(?:^|\s)color\s*:/);
      expect(rule).not.toMatch(/(?:^|\s)font-family\s*:/);
    }
    for (const [css, selector] of [
      [heroStyles, ".docspress-hero__eyebrow"],
      [heroStyles, ".docspress-hero__description"],
      [audienceStyles, ".docspress-audience-paths .docspress-audience-paths__eyebrow"],
      [audienceStyles, ".docspress-audience-paths .docspress-audience-paths__description"],
      [audienceStyles, ".docspress-audience-paths__card-description"],
    ]) {
      const rule = cssRule(css, selector);
      expect(rule).not.toMatch(/(?:^|\s)color\s*:/);
      expect(rule).not.toMatch(/(?:^|\s)font-family\s*:/);
    }
    expect(heroStyles).not.toMatch(
      /:where\(\.docspress-hero__button--primary\)\s*\{[^}]*(?:background|color)\s*:/s
    );
    expect(heroStyles).toMatch(
      /\.docspress-hero__button--secondary\.wp-element-button\s*\{[^}]*background:\s*transparent;[^}]*color:\s*inherit;/s
    );
    for (const source of [heroEditor, heroRender]) {
      expect(source).toContain("docspress-hero--has-panel-color");
      expect(source).toContain("docspress-hero--has-text-color");
    }
    for (const source of [audienceEditor, audienceRender]) {
      expect(source).toContain("docspress-audience-paths--has-panel-color");
      expect(source).not.toContain("docspress-audience-paths--has-text-color");
    }
    expect(audienceEditor).toContain("tagName: 'h3'");
    expect(audienceEditor).toContain("tagName: 'p'");
    expect(audienceRender).toContain('<h3 class="docspress-audience-paths__card-title">');
    expect(audienceRender).toContain('<p class="docspress-audience-paths__card-description">');
  });

  it("makes the documentation sidebar collapsible from block settings", async () => {
    const editor = await fs.readFile(
      path.join(root, "theme", "assets", "js", "block-components.js"),
      "utf8"
    );
    const php = await fs.readFile(path.join(root, "theme", "inc", "blocks.php"), "utf8");
    const runtime = await fs.readFile(path.join(root, "theme", "assets", "js", "docs.js"), "utf8");
    const styles = await fs.readFile(path.join(root, "theme", "style.css"), "utf8");

    for (const attribute of ["showCollapse", "startCollapsed", "collapseLabel", "expandLabel"]) {
      expect(editor).toContain(attribute);
      expect(php).toContain(`'${attribute}'`);
    }
    expect(editor).toContain("Sidebar collapse button");
    expect(editor).toContain("Show collapse circle");
    expect(editor).toContain("Display the circular desktop control on the sidebar divider.");
    expect(editor).toContain("Start collapsed on desktop");
    expect(php).toContain("data-sidebar-collapse-toggle");
    expect(runtime).toContain("applySidebarCollapsed");
    expect(runtime).toContain("desktopSidebarMedia");
    expect(styles).toContain(".docs-sidebar.is-sidebar-collapsed");
    expect(styles).toContain(".docs-shell.is-sidebar-collapsed");
    expect(styles).toContain("border-radius: 50%");
    expect(styles).toContain("transform: translate(50%, -50%)");
    expect(styles).toContain(".sidebar-collapse-toggle::before");
    expect(styles).toContain("inset: -5px");
    expect(styles).toContain(".editor-styles-wrapper .docs-sidebar.is-sidebar-collapsed .sidebar-collapse-label");
    expect(styles).toContain("left: calc(100% + 10px)");
  });

  it("keeps header and documentation navigation state in sync with the URL", async () => {
    const runtime = await fs.readFile(path.join(root, "theme", "assets", "js", "docs.js"), "utf8");
    const styles = await fs.readFile(path.join(root, "theme", "style.css"), "utf8");

    expect(runtime).toContain("function enhanceCurrentNavigation(navigation)");
    expect(runtime).toContain("link.classList.toggle('is-current-page', exact)");
    expect(runtime).toContain("link.classList.toggle('is-current-ancestor', ancestor)");
    expect(runtime).toContain("link.setAttribute('aria-current', 'page')");
    expect(runtime).toContain("enhanceCurrentNavigation(document.querySelector('.primary-navigation'))");
    expect(runtime).toContain("enhanceCurrentNavigation(docsNav)");
    expect(styles).toContain('.docs-nav a[aria-current="page"]');
  });

  it("keeps command-search data and controls available in rendered block templates", async () => {
    const php = await fs.readFile(path.join(root, "theme", "inc", "blocks.php"), "utf8");
    const runtime = await fs.readFile(path.join(root, "theme", "assets", "js", "docs.js"), "utf8");
    const styles = await fs.readFile(path.join(root, "theme", "style.css"), "utf8");

    expect(php).toContain('type="application/json" data-docspress-search-data');
    expect(php).toContain('data-docs-search-trigger aria-label="<?php echo esc_attr( $label ); ?>"');
    expect(runtime).toContain("document.querySelector('[data-docspress-search-data]')");
    expect(styles).not.toMatch(/^\.align(?:full|wide)\s*\{/m);
  });

  it("previews global styles against the complete documentation template", async () => {
    const functions = await fs.readFile(path.join(root, "theme", "functions.php"), "utf8");
    const preview = await fs.readFile(
      path.join(root, "theme", "assets", "js", "site-editor-preview.js"),
      "utf8"
    );
    const docsTemplate = await fs.readFile(
      path.join(root, "theme", "templates", "page.html"),
      "utf8"
    );

    expect(functions).toContain("function docspress_site_editor_preview_context()");
    expect(functions).toContain(
      "function docspress_redirect_site_editor_design_preview()"
    );
    expect(functions).toContain("get_stylesheet() . '//page'");
    expect(functions).toContain("get_stylesheet() . '//archive'");
    expect(functions).toMatch(/'postType'\s*=>\s*'wp_template'/);
    expect(functions).not.toContain("'postType' => 'page',");
    expect(functions).toContain("'enqueue_block_editor_assets'");
    expect(functions).toContain(
      "add_action( 'admin_init', 'docspress_redirect_site_editor_design_preview' )"
    );
    expect(functions).toContain("wp_safe_redirect( $url )");
    expect(functions).toContain("'docspress-site-editor-preview'");
    expect(functions).toContain("array( 'wp-compose', 'wp-element', 'wp-hooks' )");
    expect(preview).toContain("const previewRoute = url.searchParams.get( 'p' )");
    expect(preview).toContain("previewRoute === null || previewRoute === '/'");
    expect(preview).toContain("! isDesignPreview && previewRoute !== '/styles'");
    expect(preview).toContain("url.searchParams.set( 'p', '/' )");
    expect(preview).toContain("url.searchParams.get( 'postType' ) === 'page'");
    expect(preview).toContain("hasEntityContext && ! isLegacyPagePreview");
    expect(preview).toContain("url.searchParams.set( 'postType', context.postType )");
    expect(preview).toContain("url.searchParams.set( 'postId', String( context.postId ) )");
    expect(preview).toContain("window.location.replace( url.toString() )");
    expect(preview).toContain("'editor.BlockEdit'");
    expect(preview).toContain("'docspress/archive-query-preview'");
    expect(preview).toContain("props.name !== 'core/query'");
    expect(preview).toContain("inherit: false");
    expect(docsTemplate).toContain('"slug":"header"');
    expect(docsTemplate).toContain("wp:docspress/docs-navigation");
    expect(docsTemplate).toContain("wp:docspress/table-of-contents");
    expect(docsTemplate).toContain('"slug":"footer"');
  });

  it("does not load classic Customizer architecture", async () => {
    const functions = await fs.readFile(path.join(root, "theme", "functions.php"), "utf8");

    expect(functions).not.toContain("inc/customizer.php");
    await expect(fs.access(path.join(root, "theme", "inc", "customizer.php"))).rejects.toThrow();
    await expect(fs.access(path.join(root, "theme", "assets", "js", "customizer-preview.js"))).rejects.toThrow();
  });

  it("documents a Site Editor destination for every retired Customizer setting", async () => {
    const audit = await fs.readFile(
      path.join(root, "docs", "reference", "site-editor-migration-audit.md"),
      "utf8"
    );

    for (const setting of legacyCustomizerSettings) {
      expect(audit).toContain(`\`${setting}\``);
    }
  });
});
