import logging

from rest_framework import permissions

class IsUserFeedbackOwner(permissions.BasePermission):
    """
    Permissão personalizada para permitir POST e GET apenas para usuários
    no grupo com a permissão 'change_yours', garantindo que o user_id
    do Feedback seja igual ao user_id do request.
    """
    def has_permission(self, request, view):

        # Validar se o user_id no corpo da requisição é igual ao user_id do request

        if (not request.user.is_superuser and request.user.has_perm("myapp.change_only_yours")
                and int(request.data.get('user')) != request.user.id):
            return False
        return True

class IsSuperUser(permissions.BasePermission):
    def has_permission(self, request, view):
        return request.user.is_superuser