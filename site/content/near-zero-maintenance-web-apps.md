title: Near Zero Maintenance Web Apps
date: "2026-04-12"
category: Software Architecture
---

Traditional web apps rot. Every dependency is a ticking clock.

You ship something that works. Six months later you're updating packages not because anything broke, but because you got a security warning or a deprecation notice. Now you have to upgrade X, which means you have to upgrade Y, and something breaks along the way.

The maintenance burden scales with your dependency count, not your feature count.

## The Package Trap

A package is someone else's code that you depend on but don't control.

You pull in a library for 10% of what it does. Now you own 100% of its update cycle. Its maintainer changes a return type. Its transitive dependency has a security vulnerability. Its major version drops support for your PHP version. None of this has anything to do with your product.

You weren't building features. You were servicing debt on code you didn't write.

Every package is a subscription. Almost none of them are worth paying. Most are boilerplate that someone was excited to publish and we all got used to installing because it felt smart.

The logic was DRY. Reuse existing code so you don't have to maintain it. But you do maintain it. You just maintain code you don't understand. You lose context on how your own system works. You drill into source code you didn't write. You override and extend things that were never designed for your use case. If you owned that code, you'd understand it and could change it in minutes.

## AI Changes the Math

The reason people reached for packages was mechanical cost. Writing an HTTP client from scratch took time. Parsing dates, handling encryption, validating input. Packages made that free.

But packages were never free. The upfront cost was low. The ongoing cost was hidden. Updates, compatibility, security monitoring, version pinning, lock file conflicts. You traded one-time effort for indefinite maintenance.

AI flips the trade. You describe what you need. It generates exactly that. No extra surface area, no upstream maintainer, no dependency graph. You own the code. It does what you need and nothing else.

The one-time effort that justified packages is now close to zero. The ongoing cost of packages hasn't changed. The math no longer works.

## Deploy and Walk Away

Stop depending on moving parts.

No packages means no upstream breakage. No supply chain risk. Deploy it and walk away. It runs until the server dies or you decide to change something.

Near zero maintenance cost at rest.

## Proof of Concept

I built [euchre.club](https://euchre.club) this way. 29K lines of PHP. No framework. No production dependencies. No npm. No build step. AI opponents that run Monte Carlo simulations. Real-time multiplayer over a custom Rust WebSocket server. Tournaments, leaderboards, replays, emotes.

It just runs. I deploy changes when I want features, not because something upstream broke.

This isn't theoretical. It's a production PWA wrapped in the Google Play Store using Bubblewrap. One codebase, one deploy, and both the web and the Play Store get the update.

## The Default Should Flip

The industry default is packages first, hand-written code as a last resort.

Flip that.

Write it yourself. Let AI handle the mechanical cost. Sometimes your app needs a high complexity component that isn't worth trying to own. You'll know when that happens. It's extremely rare.

Most of your dependencies are paying rent on an apartment you could own.
