import { ref, onMounted } from 'vue';

interface Logo {
    name: string;
    lightPath: string | null;
    darkPath: string | null;
    faviconPath: string | null;
}

interface LogoResponse {
    hasLogo: boolean;
    logo: Logo | null;
}

export function useLogo() {
    const hasLogo = ref(false);
    const logo = ref<Logo | null>(null);
    const isLoading = ref(true);
    const error = ref<string | null>(null);

    const fetchLogo = async () => {
        try {
            isLoading.value = true;
            const response = await fetch('/api/logo');
            const data: LogoResponse = await response.json();
            
            hasLogo.value = data.hasLogo;
            logo.value = data.logo;
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Failed to fetch logo';
            hasLogo.value = false;
            logo.value = null;
        } finally {
            isLoading.value = false;
        }
    };

    onMounted(() => {
        fetchLogo();
    });

    const getLightLogo = () => logo.value?.lightPath || null;
    const getDarkLogo = () => logo.value?.darkPath || null;
    const getFavicon = () => logo.value?.faviconPath || null;

    return {
        hasLogo,
        logo,
        isLoading,
        error,
        getLightLogo,
        getDarkLogo,
        getFavicon,
        fetchLogo,
    };
}
