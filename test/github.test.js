import { describe, expect, it, vi } from "vitest";
import { DOCSPRESS_PR_MARKER, GitHubPullRequestClient } from "../src/github.js";

function mockOctokit(options = {}) {
  const branchExists = options.branchExists || false;
  const pulls = (options.pulls || []).map((pull) => ({
    head: { ref: "docspress/wordpress-sync", repo: { full_name: "o/r" } },
    ...pull
  }));
  const git = {
    getRef: vi.fn(async ({ ref }) => {
      if (ref === "heads/docspress/wordpress-sync" && !branchExists) {
        throw Object.assign(new Error("Not found"), { status: 404 });
      }
      return { data: { object: { sha: ref === "heads/main" ? "base-sha" : "branch-sha" } } };
    }),
    getCommit: vi.fn(async () => ({ data: { tree: { sha: "base-tree" } } })),
    createBlob: vi.fn(async () => ({ data: { sha: "blob-sha" } })),
    createTree: vi.fn(async () => ({ data: { sha: "new-tree" } })),
    createCommit: vi.fn(async () => ({ data: { sha: "new-commit" } })),
    createRef: vi.fn(async () => ({})),
    updateRef: vi.fn(async () => ({})),
    deleteRef: vi.fn(async () => ({}))
  };
  const pullApi = {
    list: vi.fn(async () => ({ data: pulls })),
    create: vi.fn(async () => ({ data: { number: 12, html_url: "https://github.com/o/r/pull/12" } })),
    update: vi.fn(async ({ pull_number, state }) => ({
      data: { number: pull_number, html_url: `https://github.com/o/r/pull/${pull_number}`, state }
    }))
  };
  return {
    rest: {
      repos: { get: vi.fn(async () => ({ data: { default_branch: "main" } })) },
      git,
      pulls: pullApi
    }
  };
}

function client(octokit, options = {}) {
  return new GitHubPullRequestClient({
    token: "token",
    repository: "o/r",
    octokit,
    ...options
  });
}

describe("GitHubPullRequestClient", () => {
  it("creates a managed branch, commit, and pull request", async () => {
    const octokit = mockOctokit();
    const result = await client(octokit).syncChanges([{ path: "docs/index.md", content: "# Docs\n" }]);

    expect(result.status).toBe("created");
    expect(octokit.rest.git.createRef).toHaveBeenCalledWith(expect.objectContaining({ sha: "new-commit" }));
    expect(octokit.rest.git.createCommit).toHaveBeenCalledWith(expect.objectContaining({
      message: "docs(docs): sync changes from WordPress"
    }));
    expect(octokit.rest.pulls.create).toHaveBeenCalledWith(expect.objectContaining({
      title: "docs(docs): sync changes from WordPress",
      body: expect.stringMatching(/Summary[\s\S]*WordPress → GitHub[\s\S]*Review and merge/)
    }));
  });

  it("uses a conventional multi-file title and a descriptive file count", async () => {
    const octokit = mockOctokit();
    await client(octokit).syncChanges([
      { path: "docs/guide.md", content: "# Guide\n" },
      { path: "docs/api.md", content: "# API\n" }
    ]);

    expect(octokit.rest.git.createCommit).toHaveBeenCalledWith(expect.objectContaining({
      message: "docs(wordpress): sync 2 files from WordPress"
    }));
    expect(octokit.rest.pulls.create).toHaveBeenCalledWith(expect.objectContaining({
      title: "docs(wordpress): sync 2 files from WordPress",
      body: expect.stringContaining("2 Markdown files")
    }));
  });

  it("honors an explicit pull request title override", async () => {
    const octokit = mockOctokit();
    await client(octokit, { title: "docs(api): import editorial updates" })
      .syncChanges([{ path: "docs/api.md", content: "# API\n" }]);

    expect(octokit.rest.git.createCommit).toHaveBeenCalledWith(expect.objectContaining({
      message: "docs(api): import editorial updates"
    }));
    expect(octokit.rest.pulls.create).toHaveBeenCalledWith(expect.objectContaining({
      title: "docs(api): import editorial updates"
    }));
  });

  it("force-refreshes an existing managed rolling pull request", async () => {
    const octokit = mockOctokit({
      branchExists: true,
      pulls: [{ number: 9, state: "open", body: DOCSPRESS_PR_MARKER, html_url: "https://github.com/o/r/pull/9" }]
    });
    const result = await client(octokit).syncChanges([{ path: "docs/index.md", content: "# Docs\n" }]);

    expect(result.status).toBe("updated");
    expect(octokit.rest.git.updateRef).toHaveBeenCalledWith(expect.objectContaining({ force: true }));
    expect(octokit.rest.pulls.update).toHaveBeenCalledWith(expect.objectContaining({ pull_number: 9 }));
  });

  it("finds a managed closed pull request by filtering the repository pull list", async () => {
    const octokit = mockOctokit({
      branchExists: true,
      pulls: [{
        number: 9,
        state: "closed",
        body: DOCSPRESS_PR_MARKER,
        html_url: "https://github.com/o/r/pull/9"
      }]
    });

    const result = await client(octokit).syncChanges([{ path: "docs/index.md", content: "# Docs\n" }]);

    expect(result.status).toBe("created");
    expect(octokit.rest.git.updateRef).toHaveBeenCalledWith(expect.objectContaining({ force: true }));
    expect(octokit.rest.pulls.create).toHaveBeenCalledOnce();
    expect(octokit.rest.pulls.list).toHaveBeenCalledWith(expect.not.objectContaining({ head: expect.anything() }));
  });

  it("closes and removes a managed proposal when changes disappear", async () => {
    const octokit = mockOctokit({
      branchExists: true,
      pulls: [{ number: 9, state: "open", body: DOCSPRESS_PR_MARKER, html_url: "https://github.com/o/r/pull/9" }]
    });
    const result = await client(octokit).syncChanges([]);

    expect(result.status).toBe("closed");
    expect(octokit.rest.git.deleteRef).toHaveBeenCalled();
  });
});
