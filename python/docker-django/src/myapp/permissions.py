from rest_framework import permissions

class IsUserFeedbackOwner(permissions.BasePermission):
    def has_permission(self, request, view):
        if (not request.user.is_superuser and request.user.has_perm('myapp.change_only_yours')
                and int(request.data.get('user')) != request.user.id):
            return False
        return True

