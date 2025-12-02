<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Button from '@/components/ui/button/Button.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

interface Submission {
    id: number;
    status: string;
    grade?: number;
    feedback?: string;
    file_path?: string;
    created_at: string;
    notes?: string;
}

interface Assignment {
    id: number;
    title: string;
    description: string;
    file_path: string;
    due_date: string;
    category?: string;
    is_active: boolean;
    created_at: string;
    submission?: Submission;
}

interface Props {
    assignments: Assignment[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Assignments',
        href: '#',
    },
];

const selectedAssignment = ref<Assignment | null>(null);
const filterStatus = ref<'all' | 'active' | 'inactive' | 'pending'>('all');
const showUploadDialog = ref(false);
const uploadForm = useForm({
    file: null as File | null,
    notes: '',
});
const currentUploadAssignment = ref<Assignment | null>(null);
const fileInput = ref<HTMLInputElement | null>(null);
const uploadProgress = ref(0);
const isUploading = ref(false);

// Filter assignments based on status
const filteredAssignments = computed(() => {
    return props.assignments.filter(assignment => {
        switch (filterStatus.value) {
            case 'active':
                return assignment.is_active;
            case 'inactive':
                return !assignment.is_active;
            case 'pending':
                return assignment.is_active && !isOverdue(assignment.due_date);
            case 'all':
            default:
                return true;
        }
    });
});

const handleAssignmentClick = (assignment: Assignment) => {
    selectedAssignment.value = assignment;
};

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const isOverdue = (dueDate: string) => {
    return new Date(dueDate) < new Date();
};

const openUploadDialog = (assignment: Assignment) => {
    currentUploadAssignment.value = assignment;
    showUploadDialog.value = true;
};

const closeUploadDialog = () => {
    showUploadDialog.value = false;
    currentUploadAssignment.value = null;
    uploadForm.reset();
    uploadProgress.value = 0;
    isUploading.value = false;
};

const handleFileSelect = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = target.files;
    if (files && files.length > 0) {
        uploadForm.file = files[0];
    }
};

const submitAssignment = async () => {
     if (!currentUploadAssignment.value || !uploadForm.file) {
         alert('Please select a file');
         return;
     }

     isUploading.value = true;
     uploadProgress.value = 0;

     // Create FormData for file upload
     const formData = new FormData();
     formData.append('file', uploadForm.file);
     formData.append('notes', uploadForm.notes);

     // Get the upload URL
     const assignmentId = currentUploadAssignment.value.id;
     const uploadUrl = `/assignments/${assignmentId}/upload`;
     console.log('Upload URL:', uploadUrl);
     console.log('Sending upload request with file:', uploadForm.file.name, 'size:', uploadForm.file.size);

     // Use fetch API with proper headers
     try {
         // Get CSRF token from Inertia page props
         const page = usePage();
         const csrfToken = page.props.csrf_token as string;
         console.log('CSRF Token:', csrfToken ? 'present' : 'missing');

         const headers: Record<string, string> = {
             'Accept': 'application/json',
             'X-CSRF-TOKEN': csrfToken,
         };

         const response = await fetch(uploadUrl, {
             method: 'POST',
             body: formData,
             credentials: 'same-origin',
             headers: headers,
         });

         console.log('Upload completed with status:', response.status);
         
         if (response.ok) {
             uploadProgress.value = 100;
             const data = await response.json();
             setTimeout(() => {
                 closeUploadDialog();
                 // Reload the page to show updated submission status
                 window.location.reload();
             }, 500);
         } else {
             console.error('Upload failed with status:', response.status);
             const errorText = await response.text();
             console.error('Response:', errorText);
             isUploading.value = false;
             uploadProgress.value = 0;
             alert('Failed to upload assignment. Server error: ' + response.status);
         }
     } catch (error) {
         console.error('Upload error:', error);
         isUploading.value = false;
         uploadProgress.value = 0;
         alert('Failed to upload assignment. Network error. Please try again.');
     }
 };
</script>

<template>
    <Head title="Assignments" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="mb-6">
                <h1 class="text-3xl font-bold">Assignments</h1>
                <p class="text-muted-foreground mt-2">View and download your assignments</p>
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-2 mb-4">
                <Button
                    :variant="filterStatus === 'all' ? 'default' : 'outline'"
                    @click="filterStatus = 'all'"
                    class="transition-all"
                >
                    All Assignments
                </Button>
                <Button
                    :variant="filterStatus === 'active' ? 'default' : 'outline'"
                    @click="filterStatus = 'active'"
                    class="transition-all"
                >
                    <svg
                        class="h-4 w-4 mr-1.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    Active
                </Button>
                <Button
                    :variant="filterStatus === 'pending' ? 'default' : 'outline'"
                    @click="filterStatus = 'pending'"
                    class="transition-all"
                >
                    <svg
                        class="h-4 w-4 mr-1.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    Pending
                </Button>
                <Button
                    :variant="filterStatus === 'inactive' ? 'default' : 'outline'"
                    @click="filterStatus = 'inactive'"
                    class="transition-all"
                >
                    <svg
                        class="h-4 w-4 mr-1.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2M12 8v4m0 4v4"
                        />
                    </svg>
                    Inactive
                </Button>
            </div>

            <!-- Assignments Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div v-if="filteredAssignments.length === 0" class="col-span-full text-center py-12">
                    <svg
                        class="mx-auto h-12 w-12 text-muted-foreground"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                    <p class="mt-4 text-muted-foreground">No assignments yet</p>
                </div>

                <template v-else>
                    <Card
                        v-for="assignment in filteredAssignments"
                        :key="assignment.id"
                        :class="[
                            'overflow-hidden transition-all duration-200',
                            assignment.is_active
                                ? 'cursor-pointer border-sidebar-border/70 dark:border-sidebar-border hover:border-sidebar-border hover:shadow-md dark:hover:shadow-lg hover:scale-105'
                                : 'cursor-not-allowed border-sidebar-border/30 dark:border-sidebar-border/40 opacity-60',
                        ]"
                        @click="assignment.is_active && handleAssignmentClick(assignment)"
                    >
                        <CardHeader class="pb-3 relative">
                            <div v-if="!assignment.is_active" class="absolute top-2 right-2">
                                <div class="px-2 py-1 rounded text-xs font-medium bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                    Inactive
                                </div>
                            </div>
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <CardTitle :class="['text-lg', !assignment.is_active && 'line-through']">
                                        {{ assignment.title }}
                                    </CardTitle>
                                    <CardDescription class="mt-1">
                                        <svg
                                            class="inline-block h-4 w-4 mr-1"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                            />
                                        </svg>
                                        Due: {{ formatDate(assignment.due_date) }}
                                    </CardDescription>
                                </div>
                                <div
                                    v-if="assignment.is_active"
                                    :class="[
                                        'px-2 py-1 rounded text-xs font-medium',
                                        isOverdue(assignment.due_date)
                                            ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200'
                                            : 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200',
                                    ]"
                                >
                                    {{ isOverdue(assignment.due_date) ? 'Overdue' : 'Pending' }}
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <p class="text-sm text-muted-foreground line-clamp-2">
                                {{ assignment.description || 'No description provided' }}
                            </p>
                            <div class="mt-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs text-muted-foreground">
                                        Posted: {{ formatDate(assignment.created_at) }}
                                    </span>
                                    <svg
                                        class="h-4 w-4 text-muted-foreground"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"
                                        />
                                    </svg>
                                </div>
                                <!-- Submission Status Badge -->
                                <div v-if="assignment.submission" class="text-xs mb-3">
                                    <span :class="[
                                        'inline-block px-2 py-1 rounded text-xs font-medium',
                                        assignment.submission.status === 'submitted'
                                            ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200'
                                            : assignment.submission.status === 'graded'
                                            ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200'
                                            : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200'
                                    ]">
                                        {{ assignment.submission.status === 'submitted' ? '✓ Submitted' : assignment.submission.status === 'graded' ? `✓ Graded (${assignment.submission.grade}%)` : 'Pending Review' }}
                                    </span>
                                </div>
                                <!-- Upload Button -->
                                <Button 
                                    v-if="assignment.is_active && !isOverdue(assignment.due_date)"
                                    @click.stop="openUploadDialog(assignment)"
                                    class="w-full"
                                    variant="outline"
                                >
                                    <svg
                                        class="h-4 w-4 mr-2"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"
                                        />
                                    </svg>
                                    Upload Assignment
                                </Button>
                                <Button 
                                    v-else-if="isOverdue(assignment.due_date)"
                                    disabled
                                    class="w-full"
                                >
                                    Submission Closed (Overdue)
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                </template>
            </div>
        </div>

        <!-- Assignment Details Dialog -->
        <Dialog
            v-if="selectedAssignment"
            :open="!!selectedAssignment"
            @update:open="(open) => !open && (selectedAssignment = null)"
        >
            <DialogContent class="max-w-2xl">
                <DialogHeader>
                    <DialogTitle>{{ selectedAssignment.title }}</DialogTitle>
                    <DialogDescription>
                        Assignment Details
                    </DialogDescription>
                </DialogHeader>
                
                <!-- Inactive Notice -->
                <div v-if="!selectedAssignment.is_active" class="p-4 rounded-lg bg-yellow-50 border border-yellow-200 dark:bg-yellow-900/20 dark:border-yellow-700">
                    <div class="flex items-start gap-3">
                        <svg
                            class="h-5 w-5 text-yellow-600 dark:text-yellow-500 flex-shrink-0 mt-0.5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4v2m0 0a9 9 0 110-18 9 9 0 010 18z"
                            />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                This assignment is currently inactive
                            </p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                Files and details are not available until the instructor activates this assignment.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium mb-2">Description</h4>
                        <p class="text-sm text-muted-foreground">
                            {{ selectedAssignment.description || 'No description provided' }}
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-medium mb-2 text-sm">Posted Date</h4>
                            <p class="text-sm text-muted-foreground">
                                {{ formatDate(selectedAssignment.created_at) }}
                            </p>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2 text-sm">Due Date</h4>
                            <p
                                :class="[
                                    'text-sm font-medium',
                                    isOverdue(selectedAssignment.due_date)
                                        ? 'text-red-600 dark:text-red-400'
                                        : 'text-green-600 dark:text-green-400',
                                ]"
                            >
                                {{ formatDate(selectedAssignment.due_date) }}
                            </p>
                        </div>
                    </div>

                    <!-- File Section - Only show if active -->
                    <div v-if="selectedAssignment.is_active">
                        <div
                            v-if="selectedAssignment.file_path"
                            class="p-4 rounded-lg bg-accent/10 border border-accent/30"
                        >
                            <p class="text-sm font-medium mb-2">Attachment</p>
                            <a
                                :href="`/storage/${selectedAssignment.file_path}`"
                                target="_blank"
                                class="text-accent hover:underline text-sm flex items-center gap-1"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                    />
                                </svg>
                                Download File
                            </a>
                        </div>
                        <Button class="w-full" as-child>
                            <a
                                v-if="selectedAssignment.file_path"
                                :href="`/storage/${selectedAssignment.file_path}`"
                                target="_blank"
                            >
                                Download Assignment
                            </a>
                            <span v-else>No File Available</span>
                        </Button>
                    </div>

                    <!-- Disabled message for inactive -->
                    <div v-else class="pt-4">
                        <Button class="w-full" disabled>
                            Download Not Available - Assignment Inactive
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Upload Assignment Dialog -->
        <Dialog
            :open="showUploadDialog"
            @update:open="(open) => !open && closeUploadDialog()"
        >
            <DialogContent class="max-w-lg">
                <DialogHeader>
                    <DialogTitle>Upload Assignment</DialogTitle>
                    <DialogDescription>
                        Submit your assignment for {{ currentUploadAssignment?.title }}
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-4">
                    <!-- File Upload Area -->
                    <div class="border-2 border-dashed border-sidebar-border rounded-lg p-6">
                        <input
                            type="file"
                            ref="fileInput"
                            @change="handleFileSelect"
                            class="hidden"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.txt,.jpg,.jpeg,.png,.webp,.zip,.rar"
                        />
                        <div class="text-center cursor-pointer" @click="$refs.fileInput?.click()">
                            <svg
                                class="mx-auto h-12 w-12 text-muted-foreground mb-2"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"
                                />
                            </svg>
                            <p class="text-sm font-medium">
                                {{ uploadForm.file?.name || 'Click to upload or drag and drop' }}
                            </p>
                            <p class="text-xs text-muted-foreground mt-1">
                                PDF, DOC, XLS, TXT, Images (Max 100MB)
                            </p>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div>
                        <label class="text-sm font-medium block mb-2">
                            Notes (Optional)
                        </label>
                        <textarea
                            v-model="uploadForm.notes"
                            placeholder="Add any notes about your submission..."
                            class="w-full px-3 py-2 text-sm border border-sidebar-border rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent"
                            rows="3"
                        />
                    </div>

                    <!-- Upload Progress Section -->
                    <div v-if="isUploading" class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Uploading...</span>
                            <span class="text-sm font-bold text-accent">{{ uploadProgress }}%</span>
                        </div>
                        <div class="w-full bg-sidebar-border rounded-full h-2 overflow-hidden">
                            <div
                                class="bg-accent h-full transition-all duration-300 ease-out"
                                :style="{ width: `${uploadProgress}%` }"
                            ></div>
                        </div>
                        <div v-if="uploadProgress === 100" class="text-sm text-green-600 dark:text-green-400 font-medium">
                            ✓ Upload complete - Processing...
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div v-if="!isUploading" class="flex gap-3 justify-end pt-4">
                        <Button
                            variant="outline"
                            @click="closeUploadDialog"
                        >
                            Cancel
                        </Button>
                        <Button
                            @click="submitAssignment"
                            :disabled="!uploadForm.file"
                        >
                            Submit
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
