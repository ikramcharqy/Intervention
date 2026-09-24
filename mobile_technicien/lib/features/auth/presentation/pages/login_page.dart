import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_technicien/core/theme/app_theme.dart';
import 'package:mobile_technicien/core/widgets/app_logo.dart';
import 'package:mobile_technicien/features/auth/presentation/bloc/auth_bloc.dart';

/// Écran de connexion — reprend la mise en page du mockup fourni (champs
/// fins avec icône, bouton LOGIN plein largeur, bannière d'erreur rouge
/// au-dessus du formulaire, liens "Forgot password"/"Login with Face ID"
/// sous le bouton) avec l'identité TechniTrack et AuthBloc inchangé.
/// "Forgot password" et "Login with Face ID" sont volontairement des
/// placeholders (message "à venir" au tap, même convention que
/// ProfilePage) : aucun endpoint de réinitialisation non-authentifiée
/// n'existe côté API, et local_auth est désactivé sur Flutter Web (kIsWeb,
/// cf. BiometricLockService) — les vrais flux restent à construire
/// séparément avant d'être branchés ici.
class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _obscurePassword = true;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: SafeArea(
        child: BlocConsumer<AuthBloc, AuthState>(
          listener: (context, state) {},
          builder: (context, state) {
            final isLoading = state is AuthLoading;
            final errorMessage = state is AuthUnauthenticated ? state.message : null;

            return Align(
              alignment: Alignment.topCenter,
              child: ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 380),
                child: SingleChildScrollView(
                  padding: const EdgeInsets.symmetric(horizontal: 28, vertical: 24),
                  child: Form(
                    key: _formKey,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        const SizedBox(height: 32),
                        const Center(
                          child: AppLogo(height: 52),
                        ),
                        const SizedBox(height: 10),
                        Center(
                          child: Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                            decoration: BoxDecoration(
                              color: AppColors.brandLight,
                              borderRadius: BorderRadius.circular(AppRadius.pill),
                            ),
                            child: const Text(
                              'ESPACE TECHNICIEN',
                              style: TextStyle(
                                color: AppColors.brandDark,
                                fontSize: 11,
                                fontWeight: FontWeight.w800,
                                letterSpacing: 0.6,
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(height: 32),
                        if (errorMessage != null) ...[
                          _ErrorBanner(message: errorMessage),
                          const SizedBox(height: 14),
                        ],
                        _LoginField(
                          controller: _emailController,
                          icon: Icons.person_outline,
                          hint: 'Adresse email',
                          keyboardType: TextInputType.emailAddress,
                          hasError: errorMessage != null,
                          validator: (v) => (v == null || v.isEmpty) ? 'Email requis' : null,
                        ),
                        const SizedBox(height: 12),
                        _LoginField(
                          controller: _passwordController,
                          icon: Icons.lock_outline,
                          hint: 'Mot de passe',
                          obscureText: _obscurePassword,
                          hasError: errorMessage != null,
                          suffixIcon: IconButton(
                            icon: Icon(
                              _obscurePassword ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                              color: AppColors.neutral,
                              size: 19,
                            ),
                            onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
                          ),
                          validator: (v) => (v == null || v.isEmpty) ? 'Mot de passe requis' : null,
                        ),
                        const SizedBox(height: 22),
                        SizedBox(
                          height: 48,
                          child: FilledButton(
                            style: FilledButton.styleFrom(
                              backgroundColor: AppColors.brand,
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                            ),
                            onPressed: isLoading ? null : () => _submit(context),
                            child: isLoading
                                ? const SizedBox(
                                    width: 20,
                                    height: 20,
                                    child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                                  )
                                : const Text(
                                    'LOGIN',
                                    style: TextStyle(fontWeight: FontWeight.bold, letterSpacing: 0.5),
                                  ),
                          ),
                        ),
                        const SizedBox(height: 18),
                        _PlaceholderLinksRow(
                          onTap: (label) => ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(content: Text('$label : fonctionnalité à venir.')),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            );
          },
        ),
      ),
    );
  }

  void _submit(BuildContext context) {
    if (!_formKey.currentState!.validate()) return;
    context.read<AuthBloc>().add(AuthLoginRequested(
          email: _emailController.text.trim(),
          password: _passwordController.text,
        ));
  }
}

class _ErrorBanner extends StatelessWidget {
  final String message;
  const _ErrorBanner({required this.message});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: AppColors.danger.withValues(alpha: 0.08),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.danger.withValues(alpha: 0.3)),
      ),
      child: Row(
        children: [
          const Icon(Icons.error_outline, color: AppColors.danger, size: 18),
          const SizedBox(width: 8),
          Expanded(
            child: Text(message, style: const TextStyle(color: AppColors.danger, fontSize: 13)),
          ),
        ],
      ),
    );
  }
}

class _LoginField extends StatelessWidget {
  final TextEditingController controller;
  final IconData icon;
  final String hint;
  final bool obscureText;
  final bool hasError;
  final TextInputType? keyboardType;
  final Widget? suffixIcon;
  final String? Function(String?)? validator;

  const _LoginField({
    required this.controller,
    required this.icon,
    required this.hint,
    this.obscureText = false,
    this.hasError = false,
    this.keyboardType,
    this.suffixIcon,
    this.validator,
  });

  @override
  Widget build(BuildContext context) {
    final borderColor = hasError ? AppColors.danger : AppColors.border;
    return TextFormField(
      controller: controller,
      obscureText: obscureText,
      keyboardType: keyboardType,
      validator: validator,
      style: const TextStyle(fontSize: 14),
      decoration: InputDecoration(
        hintText: hint,
        hintStyle: const TextStyle(fontSize: 13, color: AppColors.neutral),
        prefixIcon: Icon(icon, color: hasError ? AppColors.danger : AppColors.neutral, size: 18),
        suffixIcon: suffixIcon,
        filled: true,
        fillColor: Colors.white,
        isDense: true,
        contentPadding: const EdgeInsets.symmetric(vertical: 14, horizontal: 14),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.md),
          borderSide: BorderSide(color: borderColor),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.md),
          borderSide: BorderSide(color: borderColor),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.md),
          borderSide: BorderSide(color: hasError ? AppColors.danger : AppColors.brand, width: 1.5),
        ),
        errorBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(AppRadius.md),
          borderSide: const BorderSide(color: AppColors.danger),
        ),
      ),
    );
  }
}

/// Reproduit la rangée "FORGOT PASSWORD / Or / LOGIN WITH FACE ID" du
/// mockup — visuellement fidèle, mais chaque lien n'est qu'un placeholder
/// (aucun flux réel derrière) tant que le backend correspondant n'existe pas.
class _PlaceholderLinksRow extends StatelessWidget {
  final void Function(String label) onTap;
  const _PlaceholderLinksRow({required this.onTap});

  @override
  Widget build(BuildContext context) {
    const linkStyle = TextStyle(
      color: AppColors.brand,
      fontWeight: FontWeight.bold,
      fontSize: 12,
      letterSpacing: 0.4,
    );
    return Column(
      children: [
        TextButton(
          onPressed: () => onTap('Mot de passe oublié'),
          child: const Text('MOT DE PASSE OUBLIÉ', style: linkStyle),
        ),
        const Row(
          children: [
            Expanded(child: Divider(color: AppColors.border)),
            Padding(
              padding: EdgeInsets.symmetric(horizontal: 10),
              child: Text('Ou', style: TextStyle(color: AppColors.neutral, fontSize: 12)),
            ),
            Expanded(child: Divider(color: AppColors.border)),
          ],
        ),
        TextButton.icon(
          onPressed: () => onTap('Connexion avec Face ID'),
          icon: const Icon(Icons.face_retouching_natural_outlined, size: 16, color: AppColors.brand),
          label: const Text('CONNEXION AVEC FACE ID', style: linkStyle),
        ),
      ],
    );
  }
}
