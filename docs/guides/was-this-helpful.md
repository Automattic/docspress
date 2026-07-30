---
title: Collect feedback with Was This Helpful
sidebar_position: 35
---

The **DocsPress: Was This Helpful?** block gives every documentation Page a small Helpful or Not helpful prompt. WordPress stores aggregate totals with the Page, while editors can review the score and hide the prompt without deleting earlier responses.

<!-- wp:image {"url":"https://raw.githubusercontent.com/Automattic/docspress/main/docs/assets/was-this-helpful/frontend.jpg","alt":"Published documentation Page showing the Was This Helpful prompt with Yes and No choices","width":"570px","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized"><img src="https://raw.githubusercontent.com/Automattic/docspress/main/docs/assets/was-this-helpful/frontend.jpg" alt="Published documentation Page showing the Was This Helpful prompt with Yes and No choices" style="width:570px"/><figcaption class="wp-element-caption">The default Page template places the feedback prompt after the article actions and before adjacent navigation.</figcaption></figure>
<!-- /wp:image -->

## Understand the response flow

<!-- wp:docspress/flow {"start":1,"steps":[{"title":"A reader chooses a response","content":"<p>The published block sends <strong>Helpful</strong> or <strong>Not helpful</strong> to the Page-specific DocsPress REST endpoint.</p>"},{"title":"WordPress validates the Page","content":"<p>The endpoint accepts responses only for a published, public Page. Password-protected and unavailable Pages reject the request.</p>"},{"title":"The Page totals are updated","content":"<p>WordPress increments the aggregate Helpful or Not helpful metadata and returns the new counts without caching the response.</p>"},{"title":"The browser remembers its choice","content":"<p>The selected response is saved in local storage. The buttons remain selected and disabled when that browser returns to the same Page.</p>"},{"title":"An editor reviews the signal","content":"<p>The Page editor shows the helpful rate, both counts, and the total in its <strong>Page feedback</strong> panel.</p>"}]} /-->

<!-- wp:docspress/callout {"tone":"note","title":"Use it as a directional signal","content":"<p>The one-response rule is a browser convenience, not authenticated visitor tracking or server-side uniqueness. Clearing local storage, using another browser, or calling the public endpoint directly can record another response. Treat the totals as lightweight documentation feedback rather than scientific analytics.</p>","collapsible":false,"open":true} /-->

## Add or move the block

The bundled Page template includes the block by default, immediately before **DocsPress: Adjacent Navigation**.

To change its placement for every Page:

1. Open **Appearance → Editor**.
2. Choose **Design → Templates → Page**.
3. Open **List View** and select **DocsPress: Was This Helpful?**.
4. Move the block, or insert another instance from the block inserter.
5. Select **Save** and confirm the Page template change.

Removing the block or turning off **Enabled** hides the prompt everywhere that template is used. Existing Page totals are preserved.

## Customize the prompt

Select the block in the Site Editor and open **Settings → Page feedback**. The text controls change the published prompt and its completed state.

<!-- wp:docspress/fields {"title":"Was This Helpful block settings","description":"Content and visibility accepted by <code>docspress/was-this-helpful</code>.","fields":[{"name":"enabled","type":"boolean","required":false,"defaultValue":"true","description":"<p>Shows the prompt and accepts responses on public Pages that use this template.</p>","values":"true, false","deprecated":false},{"name":"question","type":"string","required":false,"defaultValue":"Was this helpful?","description":"<p>Question shown before the response buttons.</p>","values":"","deprecated":false},{"name":"helpfulLabel","type":"string","required":false,"defaultValue":"Yes","description":"<p>Label for the Helpful response.</p>","values":"","deprecated":false},{"name":"unhelpfulLabel","type":"string","required":false,"defaultValue":"No","description":"<p>Label for the Not helpful response.</p>","values":"","deprecated":false},{"name":"thanksMessage","type":"string","required":false,"defaultValue":"Thanks for your feedback.","description":"<p>Live status announced and displayed after WordPress saves a response.</p>","values":"","deprecated":false}],"searchable":true,"compact":true} /-->

Use the block’s **Styles** panel for native text, background, link, and gradient colors; typography; padding and margin; block spacing; borders and radius; minimum height; positioning; shadow; anchor; and custom CSS classes. Global Styles can set the default for every instance, while block-level changes affect only the selected template instance.

## Review feedback for one Page

Open **Pages**, edit the documentation Page, show the editor’s Settings sidebar, and expand **Page feedback**. The panel displays the Helpful rate, Helpful and Not helpful counts, and total responses. Totals are read-only in the editor.

<!-- wp:image {"url":"https://raw.githubusercontent.com/Automattic/docspress/main/docs/assets/was-this-helpful/page-editor.jpg","alt":"WordPress Page editor with the Page feedback panel showing the per-Page visibility control and response totals","width":"215px","sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full is-resized"><img src="https://raw.githubusercontent.com/Automattic/docspress/main/docs/assets/was-this-helpful/page-editor.jpg" alt="WordPress Page editor with the Page feedback panel showing the per-Page visibility control and response totals" style="width:215px"/><figcaption class="wp-element-caption">Editors can review the aggregate score and hide the prompt for this Page without erasing its counts.</figcaption></figure>
<!-- /wp:image -->

Turn off **Show feedback on this Page** when a particular Page should not ask for feedback. Save the Page to apply the change. Turning the setting back on restores the prompt with its earlier totals intact.

The per-Page control takes precedence over the template block: a Page with feedback disabled remains hidden even when the block itself is enabled.

## Know what WordPress stores

<!-- wp:docspress/fields {"title":"Page feedback metadata","description":"REST-visible metadata registered on the WordPress <code>page</code> post type.","fields":[{"name":"docspress_helpful_votes","type":"number","required":false,"defaultValue":"0","description":"<p>Aggregate Helpful responses for the Page.</p>","values":"non-negative integer","deprecated":false},{"name":"docspress_unhelpful_votes","type":"number","required":false,"defaultValue":"0","description":"<p>Aggregate Not helpful responses for the Page.</p>","values":"non-negative integer","deprecated":false},{"name":"docspress_feedback_enabled","type":"boolean","required":false,"defaultValue":"true","description":"<p>Whether the Page can render the feedback prompt.</p>","values":"true, false","deprecated":false}],"searchable":false,"compact":true} /-->

The feature stores aggregate counts, not a visitor identity or comment. The browser separately stores `docspress-feedback:<page-id>` with the selected vote so it can restore the completed state.

## Inspect the REST exchange

The block sends a public `POST` request to the Page-specific endpoint. This example remains intentionally non-runnable because it modifies Page metadata.

<!-- wp:docspress/api-request {"method":"POST","endpoint":"/wp-json/docspress/v1/feedback/123","headers":"Accept: application/json\nContent-Type: application/json","requestBody":"{\n  \"vote\": \"helpful\"\n}","requestBodyFormat":"json","responseStatus":"200 OK","responseBody":"{\n  \"saved\": true,\n  \"vote\": \"helpful\",\n  \"counts\": {\n    \"helpful\": 8,\n    \"unhelpful\": 2,\n    \"total\": 10\n  }\n}","responseBodyFormat":"json","runnable":false,"editable":false,"allowUnsafe":false,"baseUrl":"","allowedOrigins":"","timeout":10000} /-->

The request accepts only `helpful` or `unhelpful`. It returns `404` for a Page that is missing, unpublished, or not a Page; `403` for a password-protected Page; `400` for an invalid vote; and `500` when WordPress cannot update the counter. Successful responses include `Cache-Control: no-store`.

## Extend recorded responses

DocsPress fires `docspress_page_feedback_recorded` after a response is saved. Use it to forward the aggregate event to an analytics or reporting integration without changing the block.

<!-- wp:docspress/colorful-code {"language":"php","filename":"functions.php","code":"add_action(\n    'docspress_page_feedback_recorded',\n    function ( $page_id, $vote, $counts ) {\n        // Forward the aggregate event to your reporting system.\n    },\n    10,\n    3\n);","highlightedLines":"1-10","showLineNumbers":true,"caption":"The hook receives the Page ID, the saved vote, and the updated helpful, unhelpful, and total counts.","diffMode":"none","copyMode":"all","annotations":[]} /-->

If the public endpoint needs stricter abuse controls, add them at the WordPress or hosting layer and test that normal anonymous readers can still submit a response.

## Accessibility behavior

The response buttons form a labelled group tied to the visible question. The selected button uses `aria-pressed`, saving and error states are announced through a live status region, and keyboard focus remains visible. On narrow screens the prompt and actions stack vertically, and reduced-motion preferences remove nonessential transitions.
