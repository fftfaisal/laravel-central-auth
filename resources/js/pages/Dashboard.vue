<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import { Shield, AppWindow, UserCheck, Key, ExternalLink } from '@lucide/vue';
import { dashboard } from '@/routes';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Account Portal',
                href: dashboard(),
            },
        ],
    },
});

const page = usePage();
const user = page.props.auth?.user;

const connectedApps = [
    {
        name: 'Tenant App One',
        url: 'http://app-one.local.test',
        clientId: '11111111-1111-1111-1111-111111111111',
        status: 'Authorized',
        description: 'Primary customer dashboard application.',
    },
    {
        name: 'Tenant App Two',
        url: 'http://app-two.local.test',
        clientId: '22222222-2222-2222-2222-222222222222',
        status: 'Authorized',
        description: 'Secondary tenant management application.',
    },
];
</script>

<template>
    <Head title="Account Portal - Central Auth" />

    <div
        class="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6"
    >
        <!-- User Profile Banner -->
        <div
            class="rounded-2xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-full bg-primary/10 text-2xl font-bold text-primary"
                >
                    {{ user?.name?.charAt(0) || 'U' }}
                </div>
                <div>
                    <h1 class="text-xl font-bold text-foreground">
                        Welcome back, {{ user?.name }}
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        {{ user?.email }} &bull; Central Single Sign-On Account
                    </p>
                </div>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid gap-4 md:grid-cols-3">
            <div
                class="flex items-center gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4 shadow-sm"
            >
                <div class="rounded-lg bg-blue-500/10 p-3 text-blue-500">
                    <UserCheck class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">Account Status</p>
                    <p class="font-semibold text-foreground">
                        Active &amp; Verified
                    </p>
                </div>
            </div>

            <div
                class="flex items-center gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4 shadow-sm"
            >
                <div class="rounded-lg bg-emerald-500/10 p-3 text-emerald-500">
                    <AppWindow class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">
                        Connected Applications
                    </p>
                    <p class="font-semibold text-foreground">2 OAuth Tenants</p>
                </div>
            </div>

            <div
                class="flex items-center gap-4 rounded-xl border border-sidebar-border/70 bg-card p-4 shadow-sm"
            >
                <div class="rounded-lg bg-amber-500/10 p-3 text-amber-500">
                    <Shield class="h-6 w-6" />
                </div>
                <div>
                    <p class="text-xs text-muted-foreground">
                        Security Protocol
                    </p>
                    <p class="font-semibold text-foreground">
                        OAuth2 + Passport
                    </p>
                </div>
            </div>
        </div>

        <!-- Connected Apps Section -->
        <div
            class="rounded-2xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border"
        >
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-foreground">
                        Connected Tenant Applications
                    </h2>
                    <p class="text-sm text-muted-foreground">
                        Applications that use this central identity provider for
                        authentication.
                    </p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div
                    v-for="app in connectedApps"
                    :key="app.clientId"
                    class="rounded-xl border border-sidebar-border/50 p-4 transition-all hover:border-primary/50"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent"
                            >
                                <Key class="h-5 w-5 text-accent-foreground" />
                            </div>
                            <div>
                                <h3 class="font-medium text-foreground">
                                    {{ app.name }}
                                </h3>
                                <p class="text-xs text-muted-foreground">
                                    {{ app.description }}
                                </p>
                            </div>
                        </div>
                        <span
                            class="inline-flex items-center rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-medium text-emerald-500"
                        >
                            {{ app.status }}
                        </span>
                    </div>

                    <div
                        class="mt-4 flex items-center justify-between border-t border-sidebar-border/40 pt-3 text-xs text-muted-foreground"
                    >
                        <span class="truncate font-mono"
                            >Client ID:
                            {{ app.clientId.substring(0, 13) }}...</span
                        >
                        <a
                            :href="app.url"
                            target="_blank"
                            class="inline-flex items-center gap-1 font-medium text-primary hover:underline"
                        >
                            Launch App <ExternalLink class="h-3 w-3" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
