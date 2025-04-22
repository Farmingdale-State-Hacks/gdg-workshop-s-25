import { $ } from 'bun';
(async() => {
  // npx gitpick <owner>/<repo_name>/tree/<branch>/<path/to/dir> ?<name>
  const DIRS: Set<string> = new Set([
    "backend",
    "frontend",
    "ml",
    "server",
  ]);

  const REPOS: Set<string> = new Set([
    "https://github.com/WomB0ComB0/testing",
    "https://github.com/WomB0ComB0/testing",
    "https://github.com/WomB0ComB0/testing",
    "https://github.com/WomB0ComB0/testing",
  ]);

  const EXPRESSIONS: Set<string> = new Set([...REPOS].flatMap(repo => [...DIRS].map(dir => `${repo} ${dir}`)));


  // {
  //   for (const repo of REPOS) {
      
  //   }
  // }
  console.log(EXPRESSIONS);
})();
