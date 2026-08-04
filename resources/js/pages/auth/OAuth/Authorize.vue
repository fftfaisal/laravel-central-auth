<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ShieldCheck, ArrowRight, CheckCircle2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';

defineProps<{
    client: {
        id: string;
        name: string;
    };
    user: {
        name: string;
        email: string;
    };
    authToken: string;
    request: {
        state: string;
    };
    scopes: any[];
}>();
</script>

<template>
    <Head title="Authorize Application - Central Auth" />

    <div class="flex min-h-screen items-center justify-center bg-background p-4 text-foreground">
        <div class="w-full max-w-md rounded-2xl border border-sidebar-border/70 bg-card p-8 shadow-xl dark:border-sidebar-border">
            <!-- Header Icon -->
            <div class="mb-6 text-center">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                    <ShieldCheck class="h-7 w-7" />
                </div>
                <h1 class="text-xl font-bold text-foreground">Authorize {{ client.name }}</h1>
                <p class="mt-1 text-xs text-muted-foreground">Central Single Sign-On Request</p>
            </div>

            <!-- Client Info Banner -->
            <div class="mb-6 rounded-xl border border-sidebar-border/60 bg-accent/50 p-4 text-center">
                <p class="text-sm text-muted-foreground">
                    <strong class="font-semibold text-foreground">{{ client.name }}</strong> wants permission to access your central identity profile.
                </p>
            </div>

            <!-- Permissions Requested -->
            <div class="mb-6 space-y-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Permissions requested:</p>

                <div class="flex items-start gap-3 text-sm">
                    <CheckCircle2 class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" />
                    <div>
                        <p class="font-medium text-foreground">View profile information</p>
                        <p class="text-xs text-muted-foreground">Name and email address ({{ user.email }})</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 text-sm">
                    <CheckCircle2 class="mt-0.5 h-5 w-5 shrink-0 text-emerald-500" />
                    <div>
                        <p class="font-medium text-foreground">Single Sign-On Authentication</p>
                        <p class="text-xs text-muted-foreground">Sign you into {{ client.name }} automatically</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons using official Inertia <Form> component -->
            <div class="flex items-center gap-3">
                <!-- Approve Form -->
                <Form action="/oauth/authorize" method="post" v-slot="{ processing }" class="flex-1">
                    <input type="hidden" name="client_id" :value="client.id" />
                    <input type="hidden" name="auth_token" :value="authToken" />
                    <input type="hidden" name="state" :value="request?.state ?? ''" />

                    <Button
                        type="submit"
                        :disabled="processing"
                        class="w-full gap-2"
                    >
                        <span>Authorize</span>
                        <ArrowRight class="h-4 w-4" />
                    </Button>
                </Form>

                <!-- Deny Form -->
                <Form action="/oauth/authorize" method="delete" v-slot="{ processing }" class="flex-1">
                    <input type="hidden" name="client_id" :value="client.id" />
                    <input type="hidden" name="auth_token" :value="authToken" />
                    <input type="hidden" name="state" :value="request?.state ?? ''" />

                    <Button
                        type="submit"
                        variant="outline"
                        :disabled="processing"
                        class="w-full"
                    >
                        Deny
                    </Button>
                </Form>
            </div>

            <!-- Footer User Banner -->
            <div class="mt-6 border-t border-sidebar-border/50 pt-4 text-center text-xs text-muted-foreground">
                Signed in as <span class="font-medium text-foreground">{{ user.email }}</span>
            </div>
        </div>
    </div>
</template>
