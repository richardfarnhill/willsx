# PROPRIETARY SOFTWARE
This software is proprietary and confidential. No license is granted for its use, modification, or distribution.
Unauthorized deployment or distribution of this calculator is strictly prohibited.

Copyright © 2025 Richard Farnhill. All rights reserved.

# Project Rules (For Authorized Developers Only)
- 

## Proprietary Notice
- All code and documentation in this project is proprietary
- No distribution of any part of this codebase is permitted
- No public repositories or forks are allowed
- All work is confidential and subject to NDA

## Environment Rules
- Operating System: Windows
- Terminal: PowerShell
- Command Chaining: Use semicolon (;) not ampersand (&)
- File Paths: Use Windows format with backslashes or escaped forward slashes

## Code Consistency
- **Never** modify existing code without explicit request
- Always build upon existing code patterns
- Maintain current file structure
- No deletion of any files without explicit approval

## Documentation Rules
- All documentation should be maintained in the appropriate directories
- Documentation must be clear and up-to-date and placed within a dedicated DOCUMENTATION folder. The only documentation that should be placed in the root is README.MD

## Project Structure
- /DOCUMENTATION - All documentation other than README.MD
- /src: Source code only
- /scripts: Utility scripts 
- /hooks: Git hooks for automation
- Root directory: Documentation and configuration files

## Critical Files
- Never modify or delete without specific consent:
  - project-rules.md
  - configuration files

## Feature Implementation
- No unprompted feature changes
- No unprompted changes to textual content
- All changes must be explicitly requested
- Test changes before committing
- Document all new features according to best practice

## Version Control
- Main branch is primary
- Feature branches must be explicitly requested
- Feature branch workflow:
  - Create feature branches only for specific tasks
  - Always push feature branches to remote with `git push origin <branch-name>`
  - When feature work is complete, merge to main and push:
    ```
    git checkout main
    git pull origin main  # Always pull latest changes first
    git merge <feature-branch>
    git push origin main
    ```
  - Verify changes are reflected in remote repository after pushing
  - Never leave work only on a feature branch without merging to main
- After pushing changes, always sync theme files to local WordPress installation:
  ```
  Copy-Item -Path "C:\Users\richa\Dev Projects\projects\WillsX\willsx\src\themes\willsx\*" -Destination "C:\Users\richa\Local Sites\willsx\app\public\wp-content\themes\willsx\" -Recurse -Force
  ```
  - This ensures your local WordPress installation stays in sync with your repository
  - Adjust paths as needed for your local environment
  - **Note**: Git hooks are set up to automate this process. Run `hooks/install-hooks.bat` to install them.
- No force pushing without approval
- No public forking or cloning without authorization

- Commit messages follow established format:
  - "Feat(component): add new component"
  - "Fix(api): fix api error"
  - "Docs(readme): update readme"
  - "Refactor(utils): refactor utils"
  - "Style(tailwind): add new tailwind class"
  - "Test(unit): add unit test"
  - "Chore(deps): update dependencies"

- Semantic Versioning (SemVer) for release tags 
 - Format: v{MAJOR}.{MINOR}.{PATCH}
 - Example: v1.0.0, v1.1.0, v1.0.1
 - This clearly communicates the nature of changes:
 - MAJOR: Breaking changes
 - MINOR: New features, backward compatible
 - PATCH: Bug fixes, backward compatible
   - If in doubt, ask the user whether the commit represents a major, minor or patch, and apply SemVer as appropriate.  AVOID using ambiguous labels that could cause conflicts and confusion

## Deployment
- 

## Notes
- This file should be updated as new rules are established
- All rules must be followed without exception
- When in doubt, ask for clarification
- No assumptions about changes or improvements without explicit approval
- Remember that all work on this project is proprietary and confidential 

## User shortcuts
- When the user enters 's&c' (without quotation marks) please:
  1. Stage all changes: `git add .`
  2. Commit with appropriate message: `git commit -m "<type>(<scope>): <description>"`
  3. Push to current branch: `git push origin HEAD`
  4. If on a feature branch, also merge to main:
     
  - **Follow the rules in Version Control above**
  - Respect the command chaining instructions in Environment Rules section

- When the user enters 'sync-theme' (without quotation marks) please:
  1. Sync theme files to local WordPress installation:
     ```
     Copy-Item -Path "C:\Users\richa\Dev Projects\projects\WillsX\willsx\src\themes\willsx\*" -Destination "C:\Users\richa\Local Sites\willsx\app\public\wp-content\themes\willsx\" -Recurse -Force
     ```
  2. Confirm the sync was successful

- When the user enters 'install-hooks' (without quotation marks) please:
  1. Run the hook installation script:
     ```
     powershell.exe -ExecutionPolicy Bypass -File "hooks/install-hooks.ps1"
     ```
  2. Confirm the hooks were installed successfully