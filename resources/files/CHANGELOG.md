# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.4.0] - 2025-05-05

### Added
- **Word Chain Game**
  - Added word chain game with three difficulty levels and a dictionary selection for British, American, Canadian and Australian English
- **Connect Four Game**
  - Added connect four game with three difficulty levels

### Changed
- **Login**
  - The login experience has been overhauled with multiple different experiences
  

## [0.3.0] - 2025-05-04

### Added
- **Chess Game**
  - Added chess game with three difficulty levels
- **Whois Command**
  - Added whois command for domain information
- **Hangman Game**
  - Added hangman game with three difficulty levels

## [0.2.1] - 2025-04-26

### Changed
- **System Architecture**
  - Improved command responsiveness by using streaming

## [0.2.0] - 2025-04-24

### Added
- **Command System Enhancements**
  - Scaffolding command for building new commands
  - Version command for displaying application version
  - SSH command for secure connections
  - Git command for version control operations
  - Calculator command for mathematical operations
  - Date, echo, and history commands for system interaction
  - Sudo command for elevated privileges
  - Contact command for user communication
- **User Experience Improvements**
  - Random tagline generation during login
  - Support, manage and documentation pages
- **Entertainment Features**
  - Games command with multiple interactive games:
    - Number guessing game
    - Rock paper scissors
    - Global Thermonuclear War
    - Tic Tac Toe

### Changed
- **System Architecture**
  - Centralized version and name management
  - Restructured login system for improved security
  - Overhauled about command architecture
  - Updated delayed output to use streaming technology
  - Reset stale commands for better performance
- **User Interface**
  - Enhanced contact command description
  - Improved about content presentation
  - Customized error pages for better user experience
  - Updated input style during interactive commands
  - Refined help UI for improved usability
  - Optimized suggestion display after command execution
- **Bug Fixes**
  - Resolved issues with interactive commands
  - Fixed sentry configuration
  - Corrected alias functionality
  - Addressed multiple test failures

### Removed
- Deprecated command functionality
- Legacy primary styles

## [0.1.0] - 2025-04-18

### Added
- Initial project implementation 
