import fs from "node:fs/promises";
import path from "node:path";
import fg from "fast-glob";
import { upgradeLegacyBlockSyntax } from "../src/reverse.js";

const requested = process.argv.slice(2);
const files = requested.length > 0
  ? requested
  : await fg(["README.md", "docs/**/*.{md,markdown}"], {
    cwd: process.cwd(),
    onlyFiles: true
  });
let changed = 0;

for (const file of files) {
  const absolute = path.resolve(process.cwd(), file);
  const existing = await fs.readFile(absolute, "utf8");
  const upgraded = upgradeLegacyBlockSyntax(existing);
  if (upgraded === existing) {
    continue;
  }
  await fs.writeFile(absolute, upgraded);
  changed += 1;
  console.log(`Converted ${file}`);
}

console.log(`Converted ${changed} Markdown file${changed === 1 ? "" : "s"}.`);
