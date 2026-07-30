import fs from "node:fs/promises";
import { describe, expect, it } from "vitest";

describe("production documentation workflow", () => {
  it("uses the canonical docs.press REST origin so authentication survives the request", async () => {
    const workflow = await fs.readFile(".github/workflows/sync-docs.yml", "utf8");

    expect(workflow).toContain("wordpress-url: https://docs.press");
    expect(workflow).toContain("wordpress-site: docs.press");
    expect(workflow).not.toContain("fkadocs.atomicsites.blog");
    expect(workflow).toContain("wordpress-access-token: ${{ secrets.WP_ACCESS_TOKEN }}");
  });
});
