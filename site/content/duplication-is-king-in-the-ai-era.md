---
title: Duplication is King in the AI Era
date: 2026-02-06
category: Programming
---

DRY is dogma. Don't Repeat Yourself. Nobody remembers learning it, but everyone believes it. See two similar things? Extract them. Share them. Reuse them.

This has always been bad advice. AI just makes it indefensible.

## Shared Code is a Liability

The promise of shared code: change it once, it updates everywhere.

The reality: you can't change it at all.

Shared code accumulates consumers. Each consumer has slightly different needs. The shared code grows `if` branches, optional parameters, and feature flags to serve them all. Now it's complicated and changing it might break something you've never heard of. Now you're scared to touch it.

This is the trajectory of most shared application code.

## DRY Creates Fear

You know this feeling. You need to change something, but it's in a shared utility. You check who else uses it. Three places? Five? Eleven? You read each one, trying to understand if your change will break their assumptions. You're not sure. You add another parameter instead. The function now takes eight arguments.

You've made the codebase worse to avoid a problem that doesn't exist.

The problem that doesn't exist: updating code in multiple places.

## Examples

**The formatting helper.** You format dates in a few places. DRY instinct says make a shared `formatDate` function. A year later, it handles timestamps, durations, relative times, timezones, and locales. The function signature is a paragraph. Half the codebase imports it.

Duplicate version: each place formats its own date. One line each. You change one without thinking about the others.

**The API endpoint.** Multiple screens need user data. DRY instinct says one endpoint, one source of truth. Now that endpoint returns forty fields because different screens need different things. Every consumer over-fetches. The response payload is a contract you can never simplify.

Duplicate version: each screen has its own endpoint. Each returns exactly what it needs. You change one without a meeting.

## This Was Always True

Bad abstractions cost more than duplicated code ever will.

Once you create a bad abstraction, people build on top of it. They work around its quirks. They add to it. Unwinding it becomes a project. Duplicated code that turns out to be wrong? You just delete it.

And here's the thing: two pieces of code that look similar aren't the same code. They might look alike today but need to evolve differently tomorrow. DRY forces conceptually different things into lockstep because they happen to be textually similar. That's a category error.

Grep has existed for fifty years. Finding duplicated code was never the problem. The problem with shared code was never "how do I find it?" It was "what breaks when I touch it?"

The costs were always asymmetric. Duplication's cost is visible—more lines of code. Coupling's cost is hidden—fear, coordination, bugs in unrelated features, meetings. We overweighted what we could count and ignored what we couldn't.

Duplicated code is also deletable. You remove a feature, you delete its code. Done. Shared code? You check dependencies, find orphaned utilities, wonder if something else might need it someday. Deletion becomes archaeology.

## AI Makes It Undeniable

"But you have to update it in twelve places instead of one."

This was always overstated. But now it's obviously wrong.

You describe the change. AI finds every instance. It updates them all. You review the diff. Done.

The "maintenance burden" that justified decades of DRY dogma was always exaggerated. AI makes the mechanical cost of duplication close to zero. What remains is the coupling, the fear, the functions with eight parameters, and the meetings.

AI solved duplication. Coupling is still undefeated.

## The Shift

Shared code is architectural complexity. It's runtime dependency. It's fear of change.

Duplicated code is just text.

One requires careful human reasoning and coordination.
The other is find and replace.

Duplicate freely. Couple reluctantly.

When duplicated code needs to change, you change it.

When shared code needs to change, you schedule a meeting to discuss what might break.

The industry default is abstraction first, duplication as a last resort.

Flip that.

Duplicate first.
Let the differences emerge.
Extract the abstraction only when the duplication actually hurts. Most of the time, it won't.

Until then:

- Don't reach for inheritance just because two classes look similar.
- Don't hide behavior in traits to avoid copy-paste.
- Don't build "one endpoint to rule them all."

Try to create pain with duplication.

You might be surprised how long it takes.
