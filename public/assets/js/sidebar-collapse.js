(() => {

    'use strict';


    /* =========================================================
       ELEMENT
    ========================================================= */

    const shell =
        document.getElementById(
            'appShell'
        );


    const sidebar =
        document.getElementById(
            'appSidebar'
        );


    const toggleButton =
        document.getElementById(
            'sidebarToggleButton'
        );


    const closeButton =
        document.getElementById(
            'sidebarCloseButton'
        );


    const floatingOpen =
        document.getElementById(
            'sidebarFloatingOpen'
        );


    const backdrop =
        document.getElementById(
            'sidebarBackdrop'
        );


    if (
        !shell
        ||
        !sidebar
    ) {
        return;
    }


    /* =========================================================
       SETTINGS
    ========================================================= */

    const STORAGE_KEY =
        'aplikasiRemmitance-sidebar';


    const MOBILE_BREAKPOINT =
        850;


    const TRANSITION_TIME =
        430;


    let animationTimer =
        null;


    /* =========================================================
       CHECK MOBILE
    ========================================================= */

    const isMobile =
        () => {

            return (
                window.innerWidth
                <=
                MOBILE_BREAKPOINT
            );

        };


    /* =========================================================
       ANIMATION STATE
    ========================================================= */

    const markAnimating =
        () => {

            document.body
                .classList
                .add(
                    'sidebar-animating'
                );


            if (
                animationTimer
            ) {
                window.clearTimeout(
                    animationTimer
                );
            }


            animationTimer =
                window.setTimeout(
                    () => {

                        document.body
                            .classList
                            .remove(
                                'sidebar-animating'
                            );

                    },
                    TRANSITION_TIME
                );

        };


    /* =========================================================
       ACCESSIBILITY
    ========================================================= */

    const syncToggleState =
        () => {

            const desktopClosed =
                shell
                    .classList
                    .contains(
                        'sidebar-collapsed'
                    );


            const mobileOpen =
                shell
                    .classList
                    .contains(
                        'mobile-sidebar-open'
                    );


            const expanded =
                isMobile()
                    ? mobileOpen
                    : !desktopClosed;


            toggleButton
                ?.setAttribute(
                    'aria-expanded',
                    expanded
                        ? 'true'
                        : 'false'
                );


            if (
                toggleButton
            ) {

                toggleButton.title =
                    expanded
                        ? 'Tutup sidebar'
                        : 'Buka sidebar';

            }

        };


    /* =========================================================
       OPEN DESKTOP
    ========================================================= */

    const openDesktop =
        (
            save = true
        ) => {

            markAnimating();


            shell
                .classList
                .remove(
                    'sidebar-collapsed'
                );


            if (save) {

                try {

                    localStorage.setItem(
                        STORAGE_KEY,
                        'open'
                    );

                } catch (
                    error
                ) {
                    // Browser storage tidak tersedia.
                }

            }


            syncToggleState();

        };


    /* =========================================================
       CLOSE DESKTOP
    ========================================================= */

    const closeDesktop =
        (
            save = true
        ) => {

            markAnimating();


            shell
                .classList
                .add(
                    'sidebar-collapsed'
                );


            if (save) {

                try {

                    localStorage.setItem(
                        STORAGE_KEY,
                        'closed'
                    );

                } catch (
                    error
                ) {
                    // Browser storage tidak tersedia.
                }

            }


            syncToggleState();

        };


    /* =========================================================
       OPEN MOBILE
    ========================================================= */

    const openMobile =
        () => {

            markAnimating();


            shell
                .classList
                .add(
                    'mobile-sidebar-open'
                );


            document.body
                .classList
                .add(
                    'sidebar-mobile-lock'
                );


            syncToggleState();

        };


    /* =========================================================
       CLOSE MOBILE
    ========================================================= */

    const closeMobile =
        () => {

            markAnimating();


            shell
                .classList
                .remove(
                    'mobile-sidebar-open'
                );


            document.body
                .classList
                .remove(
                    'sidebar-mobile-lock'
                );


            syncToggleState();

        };


    /* =========================================================
       TOGGLE
    ========================================================= */

    const toggleSidebar =
        () => {

            if (
                isMobile()
            ) {

                if (
                    shell
                        .classList
                        .contains(
                            'mobile-sidebar-open'
                        )
                ) {

                    closeMobile();

                } else {

                    openMobile();

                }


                return;

            }


            if (
                shell
                    .classList
                    .contains(
                        'sidebar-collapsed'
                    )
            ) {

                openDesktop();

            } else {

                closeDesktop();

            }

        };


    /* =========================================================
       BUTTON EVENTS
    ========================================================= */

    toggleButton
        ?.addEventListener(
            'click',
            toggleSidebar
        );


    closeButton
        ?.addEventListener(
            'click',
            () => {

                if (
                    isMobile()
                ) {

                    closeMobile();

                } else {

                    closeDesktop();

                }

            }
        );


    floatingOpen
        ?.addEventListener(
            'click',
            () => {

                if (
                    isMobile()
                ) {

                    openMobile();

                } else {

                    openDesktop();

                }

            }
        );


    backdrop
        ?.addEventListener(
            'click',
            closeMobile
        );


    /* =========================================================
       ESC KEY
    ========================================================= */

    document.addEventListener(
        'keydown',
        event => {

            if (
                event.key !==
                'Escape'
            ) {
                return;
            }


            if (
                isMobile()
                &&
                shell
                    .classList
                    .contains(
                        'mobile-sidebar-open'
                    )
            ) {

                closeMobile();

            }

        }
    );


    /* =========================================================
       SIDEBAR LINK ON MOBILE
    ========================================================= */

    sidebar
        .querySelectorAll(
            'a'
        )
        .forEach(
            link => {

                link.addEventListener(
                    'click',
                    () => {

                        if (
                            isMobile()
                        ) {
                            closeMobile();
                        }

                    }
                );

            }
        );


    /* =========================================================
       RESTORE DESKTOP STATE
    ========================================================= */

    const restoreState =
        () => {

            if (
                isMobile()
            ) {

                shell
                    .classList
                    .remove(
                        'mobile-sidebar-open'
                    );


                document.body
                    .classList
                    .remove(
                        'sidebar-mobile-lock'
                    );


                syncToggleState();

                return;
            }


            let savedState =
                'open';


            try {

                savedState =
                    localStorage.getItem(
                        STORAGE_KEY
                    )
                    ||
                    'open';

            } catch (
                error
            ) {

                savedState =
                    'open';

            }


            if (
                savedState ===
                'closed'
            ) {

                shell
                    .classList
                    .add(
                        'sidebar-collapsed'
                    );

            } else {

                shell
                    .classList
                    .remove(
                        'sidebar-collapsed'
                    );

            }


            syncToggleState();

        };


    /* =========================================================
       RESIZE
    ========================================================= */

    let lastMobileState =
        isMobile();


    window.addEventListener(
        'resize',
        () => {

            const currentMobileState =
                isMobile();


            if (
                currentMobileState
                ===
                lastMobileState
            ) {

                return;

            }


            lastMobileState =
                currentMobileState;


            if (
                currentMobileState
            ) {

                shell
                    .classList
                    .remove(
                        'mobile-sidebar-open'
                    );


                document.body
                    .classList
                    .remove(
                        'sidebar-mobile-lock'
                    );

            } else {

                restoreState();

            }


            syncToggleState();

        }
    );


    /* =========================================================
       INIT
    ========================================================= */

    restoreState();

})();