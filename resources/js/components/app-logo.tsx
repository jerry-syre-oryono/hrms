// This component assembles the full application logo.
// It combines the AppLogoIcon component with a text span.
// You can edit the text in the `span` element below to change the application name.
import AppLogoIcon from './app-logo-icon';

export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
                <AppLogoIcon className="size-5 fill-current text-white dark:text-black" />
            </div>
            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="mb-0.5 truncate leading-tight font-semibold">
                    HRMS react
                </span>
            </div>
        </>
    );
}
