---
title: Audience Paths
sidebar_position: 20
---

Use `docspress/audience-paths` to route readers into distinct documentation branches. Each path is a complete, keyboard-accessible card with its own title, summary, action, icon, accent, and destination.

## When to use it

Choose Audience Paths when readers arrive with different goals, roles, or starting states. Good examples include “I already have docs” versus “I need to create docs,” or separate administrator, developer, and contributor journeys. Use ordinary links when the choices are secondary or differ only slightly.

## Edit the block

Edit the section eyebrow, title, description, and card copy in the canvas. Use the sidebar to set each destination, icon, accent, and new-tab behavior. You can add up to six paths, select one to three columns, center or left-align text, show path numbers, reduce spacing with **Compact layout**, or choose a theme, paper, ink, or blueprint tone.

Use the spacious layout for a landing-page decision. Use Compact layout for a router inside an article. Point paths to normal WordPress Page roots so their child Pages form predictable sidebar branches.

## Attributes

<!-- wp:docspress/fields {"title":"Audience Paths attributes","description":"Section content and layout accepted by <code>docspress/audience-paths</code>.","fields":[{"name":"eyebrow","type":"string","required":false,"defaultValue":"Choose a starting point","description":"<p>Plain-text overline.</p>","values":"","deprecated":false},{"name":"title","type":"string","required":false,"defaultValue":"Where are your docs today?","description":"<p>Section heading.</p>","values":"","deprecated":false},{"name":"description","type":"string","required":false,"defaultValue":"Follow the path that matches your repository.","description":"<p>Section summary.</p>","values":"","deprecated":false},{"name":"paths","type":"array","required":true,"defaultValue":"Two starter paths","description":"<p>One to six path objects.</p>","values":"1–6 items","deprecated":false},{"name":"columns","type":"number","required":false,"defaultValue":"2","description":"<p>Responsive column target.</p>","values":"1, 2, 3","deprecated":false},{"name":"tone","type":"enum","required":false,"defaultValue":"theme","description":"<p>Panel treatment.</p>","values":"theme, paper, ink, blueprint","deprecated":false},{"name":"textAlign","type":"enum","required":false,"defaultValue":"left","description":"<p>Card content alignment.</p>","values":"left, center","deprecated":false},{"name":"compact","type":"boolean","required":false,"defaultValue":"false","description":"<p>Reduces panel spacing, card height, and type scale.</p>","values":"true, false","deprecated":false},{"name":"showNumbers","type":"boolean","required":false,"defaultValue":"false","description":"<p>Shows each path number.</p>","values":"true, false","deprecated":false},{"name":"panelColor / accentColor","type":"string","required":false,"defaultValue":"","description":"<p>Optional hexadecimal color overrides.</p>","values":"#RRGGBB","deprecated":false}],"searchable":true,"compact":true} /-->

Each `paths` item accepts:

<!-- wp:docspress/fields {"title":"Path object","description":"Fields for one audience destination.","fields":[{"name":"title","type":"string","required":true,"defaultValue":"","description":"<p>Card heading.</p>","values":"","deprecated":false},{"name":"description","type":"string","required":false,"defaultValue":"","description":"<p>Short plain-text explanation.</p>","values":"","deprecated":false},{"name":"url","type":"url","required":false,"defaultValue":"","description":"<p>Destination. An empty URL renders a non-link card.</p>","values":"","deprecated":false},{"name":"cta","type":"string","required":false,"defaultValue":"","description":"<p>Action label.</p>","values":"","deprecated":false},{"name":"icon","type":"string","required":false,"defaultValue":"","description":"<p>Semantic icon ID selected in the editor.</p>","values":"","deprecated":false},{"name":"accent","type":"enum","required":false,"defaultValue":"blue","description":"<p>Card accent.</p>","values":"blue, gold, coral, green","deprecated":false},{"name":"newTab","type":"boolean","required":false,"defaultValue":"false","description":"<p>Opens the destination in a new tab.</p>","values":"true, false","deprecated":false}],"searchable":false,"compact":true} /-->

The icon selector includes documentation, code, site, AI, API, terminal, testing, troubleshooting, repository, security, operations, and other common documentation concepts. Legacy icon abbreviations remain compatible.

## Creative examples

### Choose by documentation state

<!-- wp:docspress/audience-paths {"anchor":"choose-your-path","align":"wide","compact":false,"eyebrow":"Choose a starting point","title":"Where are your docs today?","description":"Follow the path that matches your repository.","paths":[{"title":"I already have Markdown docs","description":"Connect an existing docs folder to WordPress and begin with a safe draft sync.","url":"/docs/publish-existing-docs/","cta":"Publish existing docs","icon":"document","accent":"blue","newTab":false},{"title":"I need to create docs","description":"Generate source-grounded documentation with AI, review it, then publish it.","url":"/docs/create-docs-with-ai/","cta":"Create docs with AI","icon":"sparkles","accent":"gold","newTab":false}],"columns":2,"tone":"theme","textAlign":"left","showNumbers":false} /-->

### Choose by role

<!-- wp:docspress/audience-paths {"eyebrow":"Pick your workspace","title":"What are you responsible for?","description":"Start with the controls, examples, and checks that match your role.","paths":[{"title":"I build integrations","description":"Learn endpoints, authentication, webhooks, and safe retry behavior.","url":"#developers","cta":"Open developer docs","icon":"code","accent":"blue","newTab":false},{"title":"I run the site","description":"Configure publishing, navigation, search, and editorial access.","url":"#administrators","cta":"Open administrator docs","icon":"site","accent":"gold","newTab":false},{"title":"I contribute fixes","description":"Set up the repository, run checks, and prepare a reviewable change.","url":"#contributors","cta":"Open contributor docs","icon":"contribute","accent":"green","newTab":false}],"columns":3,"tone":"ink","textAlign":"center","compact":false,"showNumbers":true} /-->

### Compact task router

<!-- wp:docspress/audience-paths {"eyebrow":"Jump to a task","title":"What do you need right now?","description":"Use this compact router inside a longer operations guide.","paths":[{"title":"Call the API","description":"Build and inspect a request.","url":"#api","cta":"API examples","icon":"api","accent":"blue","newTab":false},{"title":"Run a command","description":"Copy a verified terminal workflow.","url":"#terminal","cta":"Terminal steps","icon":"terminal","accent":"gold","newTab":false},{"title":"Trace a failure","description":"Follow symptoms to the next check.","url":"#diagnose","cta":"Troubleshoot","icon":"bug","accent":"coral","newTab":false},{"title":"Tune performance","description":"Measure first, then change one variable.","url":"#performance","cta":"Performance guide","icon":"performance","accent":"green","newTab":false}],"columns":2,"tone":"blueprint","textAlign":"left","compact":true,"showNumbers":false} /-->

## Published behavior and accessibility

A destination card is one native link rather than nested links, so it works without JavaScript and has a predictable keyboard focus target. New-tab destinations receive safe relationship attributes. Empty URLs intentionally render static cards.

Keep the paths mutually exclusive enough that a reader can choose quickly. Start each title with the reader’s state or goal, keep descriptions parallel, and use consistent action labels.

<!-- wp:docspress/callout {"tone":"tip","title":"Make the decision obvious","content":"<p>Readers should understand the difference between paths from the titles alone. If two cards lead to nearly the same workflow, use ordinary links inside one path instead.</p>","collapsible":false,"open":true} /-->
