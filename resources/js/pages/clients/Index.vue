<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { Plus, Trash2, Pencil, Copy, Eye, EyeOff, ShieldCheck, ExternalLink } from '@lucide/vue';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogFooter,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes';
import clientRoutes from '@/routes/clients';

interface OAuthClient {
    id: string;
    name: string;
    secret: string;
    redirect: string;
    created_at: string;
}

defineProps<{
    clients: OAuthClient[];
}>();

// --- Create Modal ---
const showCreateModal = ref(false);
const createForm = useForm({ name: '', redirect: '' });

const submitCreate = () => {
    createForm.post(clientRoutes.store().url, {
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        },
    });
};

// --- Edit Modal ---
const showEditModal = ref(false);
const editingClient = ref<OAuthClient | null>(null);
const editForm = useForm({ name: '', redirect: '' });

const openEdit = (client: OAuthClient) => {
    editingClient.value = client;
    editForm.name = client.name;
    editForm.redirect = client.redirect;
    showEditModal.value = true;
};

const submitEdit = () => {
    if (!editingClient.value) {
return;
}

    editForm.put(clientRoutes.update({ client: editingClient.value.id }).url, {
        onSuccess: () => {
            showEditModal.value = false;
            editingClient.value = null;
        },
    });
};

// --- Delete / Revoke ---
const revokeClient = (id: string) => {
    if (confirm('Are you sure? This will revoke all active tokens for this application.')) {
        router.delete(clientRoutes.destroy({ client: id }).url);
    }
};

// --- Copy to Clipboard ---
const copiedId = ref<string | null>(null);
const copyToClipboard = (text: string, key: string) => {
    navigator.clipboard.writeText(text);
    copiedId.value = key;
    setTimeout(() => (copiedId.value = null), 2000);
};

// --- Toggle secret visibility ---
const visibleSecrets = ref<Set<string>>(new Set());
const toggleSecret = (id: string) => {
    if (visibleSecrets.value.has(id)) {
        visibleSecrets.value.delete(id);
    } else {
        visibleSecrets.value.add(id);
    }
};

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Account Portal', href: dashboard() },
            { title: 'OAuth Applications', href: clientRoutes.index().url },
        ],
    },
});
</script>

<template>
    <Head title="OAuth Applications - Central Auth" />

    <h1 class="sr-only">OAuth Applications</h1>

    <div class="flex flex-col space-y-6 px-4 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <Heading
                variant="small"
                title="OAuth Applications"
                description="Manage tenant applications that use this Central SSO for authentication."
            />
            <Button @click="showCreateModal = true" class="gap-2">
                <Plus class="h-4 w-4" />
                New Application
            </Button>
        </div>

        <!-- Empty State -->
        <div
            v-if="clients.length === 0"
            class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-sidebar-border/70 py-16 text-center"
        >
            <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                <ShieldCheck class="h-7 w-7" />
            </div>
            <h3 class="text-base font-semibold text-foreground">No OAuth Applications</h3>
            <p class="mt-1 max-w-sm text-sm text-muted-foreground">
                Register your first tenant application to allow it to authenticate users via this Central SSO.
            </p>
            <Button class="mt-4 gap-2" @click="showCreateModal = true">
                <Plus class="h-4 w-4" />
                Register Application
            </Button>
        </div>

        <!-- Clients List -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
                v-for="client in clients"
                :key="client.id"
                class="rounded-2xl border border-sidebar-border/70 bg-card p-5 shadow-sm transition-all hover:border-primary/40 dark:border-sidebar-border"
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary">
                            <ShieldCheck class="h-5 w-5" />
                        </div>
                        <div>
                            <h3 class="font-semibold text-foreground">{{ client.name }}</h3>
                            <a
                                :href="client.redirect"
                                target="_blank"
                                class="inline-flex items-center gap-1 text-xs text-muted-foreground hover:text-primary"
                            >
                                {{ client.redirect }}
                                <ExternalLink class="h-3 w-3" />
                            </a>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex shrink-0 items-center gap-2">
                        <Button variant="outline" size="sm" class="gap-1.5 text-xs" @click="openEdit(client)">
                            <Pencil class="h-3.5 w-3.5" />
                            Edit
                        </Button>
                        <Button
                            variant="destructive"
                            size="sm"
                            class="gap-1.5 text-xs"
                            @click="revokeClient(client.id)"
                        >
                            <Trash2 class="h-3.5 w-3.5" />
                            Revoke
                        </Button>
                    </div>
                </div>

                <!-- Client ID & Secret -->
                <div class="mt-4 grid gap-2 rounded-xl border border-sidebar-border/50 bg-accent/30 p-4">
                    <!-- Client ID -->
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <p class="text-xs text-muted-foreground">Client ID</p>
                            <p class="mt-0.5 font-mono text-xs text-foreground">{{ client.id }}</p>
                        </div>
                        <Button
                            variant="ghost"
                            size="icon"
                            class="h-7 w-7 shrink-0"
                            @click="copyToClipboard(client.id, 'id-' + client.id)"
                            :title="copiedId === 'id-' + client.id ? 'Copied!' : 'Copy Client ID'"
                        >
                            <Copy class="h-3.5 w-3.5" :class="copiedId === 'id-' + client.id ? 'text-emerald-500' : ''" />
                        </Button>
                    </div>

                    <!-- Client Secret -->
                    <div class="flex items-center justify-between gap-2 border-t border-sidebar-border/40 pt-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs text-muted-foreground">Client Secret</p>
                            <p class="mt-0.5 truncate font-mono text-xs text-foreground">
                                {{ visibleSecrets.has(client.id) ? client.secret : '•'.repeat(Math.min(client.secret?.length ?? 32, 32)) }}
                            </p>
                        </div>
                        <div class="flex shrink-0 items-center gap-1">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-7 w-7"
                                @click="toggleSecret(client.id)"
                                :title="visibleSecrets.has(client.id) ? 'Hide Secret' : 'Reveal Secret'"
                            >
                                <EyeOff v-if="visibleSecrets.has(client.id)" class="h-3.5 w-3.5" />
                                <Eye v-else class="h-3.5 w-3.5" />
                            </Button>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-7 w-7"
                                @click="copyToClipboard(client.secret, 'secret-' + client.id)"
                                :title="copiedId === 'secret-' + client.id ? 'Copied!' : 'Copy Secret'"
                            >
                                <Copy class="h-3.5 w-3.5" :class="copiedId === 'secret-' + client.id ? 'text-emerald-500' : ''" />
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Application Modal -->
    <Dialog v-model:open="showCreateModal">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Register OAuth Application</DialogTitle>
                <DialogDescription>
                    Add a new tenant application that will authenticate users via this Central SSO.
                </DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submitCreate" class="space-y-4 py-2">
                <div class="grid gap-2">
                    <Label for="create-name">Application Name</Label>
                    <Input id="create-name" v-model="createForm.name" placeholder="e.g. Tenant App One" required />
                    <InputError :message="createForm.errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="create-redirect">Callback / Redirect URL</Label>
                    <Input
                        id="create-redirect"
                        v-model="createForm.redirect"
                        type="url"
                        placeholder="http://app-one.test/auth/callback"
                        required
                    />
                    <InputError :message="createForm.errors.redirect" />
                </div>

                <DialogFooter class="pt-2">
                    <Button type="button" variant="outline" @click="showCreateModal = false">Cancel</Button>
                    <Button type="submit" :disabled="createForm.processing" class="gap-2">
                        <Plus class="h-4 w-4" />
                        Register Application
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>

    <!-- Edit Application Modal -->
    <Dialog v-model:open="showEditModal">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Edit OAuth Application</DialogTitle>
                <DialogDescription>Update the name or callback URL for this application.</DialogDescription>
            </DialogHeader>

            <form @submit.prevent="submitEdit" class="space-y-4 py-2">
                <div class="grid gap-2">
                    <Label for="edit-name">Application Name</Label>
                    <Input id="edit-name" v-model="editForm.name" placeholder="Application name" required />
                    <InputError :message="editForm.errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="edit-redirect">Callback / Redirect URL</Label>
                    <Input
                        id="edit-redirect"
                        v-model="editForm.redirect"
                        type="url"
                        placeholder="http://app-one.test/auth/callback"
                        required
                    />
                    <InputError :message="editForm.errors.redirect" />
                </div>

                <DialogFooter class="pt-2">
                    <Button type="button" variant="outline" @click="showEditModal = false">Cancel</Button>
                    <Button type="submit" :disabled="editForm.processing" class="gap-2">
                        <Pencil class="h-4 w-4" />
                        Save Changes
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
