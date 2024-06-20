confirm() {
    # call with a prompt string or use a default
    read -r -p "${1:-Are you sure you want to continue? [y/n]:} " response
    case "$response" in
        [yY][eE][sS]|[yY]) 
            true
            ;;
        *)
            false
            ;;
    esac
}
